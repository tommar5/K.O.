<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ApplicationAgreement;
use AppBundle\Form\ApplicationAgreementType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApplicationAgreementController extends Controller
{
    use DoctrineController;

    /**
     * @Route("/application-agreement/{id}/modify")
     * @Method({"GET", "POST"})
     * @Security("is_granted('agreement-create', user)")
     * @Template
     *
     * @param ApplicationAgreement $agreement
     * @param Request              $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function modifyAction(ApplicationAgreement $agreement, Request $request)
    {
        $form = $this->createForm(new ApplicationAgreementType($agreement), $agreement);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->persist($agreement);
            $this->flush();
            $this->addFlash("success", $this->get('translator')->trans('application_agreement.flash.modified'));

            return $this->redirectToRoute('app_application_modify', ['id' => $agreement->getApplication()->getId()]);
        }

        return [
            'agreement' => $agreement,
            'form'      => $form->createView(),
        ];
    }
}
