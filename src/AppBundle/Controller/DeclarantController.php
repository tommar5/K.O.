<?php namespace AppBundle\Controller;

use AppBundle\Entity\DeclarantRequest;
use AppBundle\Entity\FileUpload;
//use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use AppBundle\Form\DeclarantRequestType;
use DataDog\PagerBundle\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DeclarantController extends Controller implements VerifyTermsInterface, ChangePasswordInterface
{
    use DoctrineController;

    /**
     * @Route("/declarant")
     * @Method("GET")
     * @Template
     * @Security("false")
     * @param Request $request
     * @return array
     * //has_role('ROLE_ADMIN')
     */
    public function indexAction(Request $request)
    {
        $declarantRequests = $this->get('em')->getRepository(DeclarantRequest::class)->createQueryBuilder('dr');

        return [
            'requests' => new Pagination($declarantRequests, $request),
        ];
    }

    /**
     * @Route("/change-declarant")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("false")
     * @param Request $request
     * @return array
     * //has_role('ROLE_RACER')
     */
    public function changeAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $existingRequest = $this->get('em')->getRepository(DeclarantRequest::class)->findOneBy([
            'racer' => $user,
            'status' => DeclarantRequest::STATUS_WAITING
        ]);

        $declarantRequest = $existingRequest ? $existingRequest : new DeclarantRequest();

        $form = $this->createForm(new DeclarantRequestType($user), $declarantRequest);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $declarantRequest->setCurrentDeclarant($user->getParent());
            $declarantRequest->setRacer($user);
            $declarantRequest->setStatus(DeclarantRequest::STATUS_WAITING);
            $this->persist($declarantRequest);
            $this->flush();
            $this->addFlash("success", $this->get('translator')->trans('declarant.flash.request_sent'));
        }

        return [
            'form' => $form->createView(),
            'request' => $declarantRequest
        ];
    }

    /**
     * @Route("/declarant/{id}/confirm")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param DeclarantRequest $declarantRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmAction(DeclarantRequest $declarantRequest)
    {
        $racer = $declarantRequest->getRacer();
        $newDeclarant = $declarantRequest->getNewDeclarant();
        $racer->setParent($newDeclarant);
        $declarantRequest->setStatus(DeclarantRequest::STATUS_CONFIRMED);

        foreach ($racer->getLicences() as $licence) {
            if (!$licence->isDriverLicence()) {
                continue;
            }

            $licence->setStatus(Licence::STATUS_CANCELLED);

            foreach ($licence->getDocuments() as $document) {
                $document->setStatus(FileUpload::STATUS_REVOKED);
            }
        }

        $this->flush();

        $this->get('mail')->user($newDeclarant, 'inform_about_racer', [
            'racer' => $racer,
        ]);

        $this->addFlash("success", $this->get('translator')->trans('declarant.flash.confirmed'));
        return $this->redirectToRoute('app_declarant_index');
    }

    /**
     * @Route("/declarant/{id}/reject")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param DeclarantRequest $declarantRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function rejectAction(DeclarantRequest $declarantRequest)
    {
        $declarantRequest->setStatus(DeclarantRequest::STATUS_REJECTED);
        $this->flush();
        $this->addFlash("danger", $this->get('translator')->trans('declarant.flash.rejected'));

        return $this->redirectToRoute('app_declarant_index');
    }


    /**
     * @Route("/declarant/{id}/info")
     * @Method({"GET"})
     * @Security("licence.getUser().getMembers().contains(user) or licence.getUser().getId() == user.getId()")
     * @param Licence $licence
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function infoAction(Licence $licence)
    {
        if (!$licence->isDeclarantLicence()) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse([
            'lasfName' => $licence->getLasfName(),
            'lasfAddress' => $licence->getLasfAddress(),
            'personalCode' => $licence->getPersonalCode(),
        ]);
    }

}
