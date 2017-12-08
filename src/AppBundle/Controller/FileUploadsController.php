<?php namespace AppBundle\Controller;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\FileUploadRepository;
use AppBundle\Entity\Application;
use AppBundle\Entity\Licence;
use AppBundle\Event\StatusChangeEvent;
use AppBundle\Event\DocumentStatusChangeEvent;
use AppBundle\Form\DocumentType;
use AppBundle\Form\Type\FileUpload\RejectType;
use AppBundle\Form\Type\FileUpload\CommentType;
use DataDog\PagerBundle\Pagination;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/files")
 */
class FileUploadsController extends Controller implements VerifyTermsInterface
{
    use DoctrineController;

    /**
     * @Route("")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $files = $this->get('em')->getRepository('AppBundle:FileUpload')->createQueryBuilder('t')
            ->join('t.user', 'u');

        $types = [
            Pagination::$filterAny => '',
            FileUpload::TYPE_DRIVERS_LICENCE => 'file_uploads.type.driver_licence',
            FileUpload::TYPE_MED_CERT => 'file_uploads.type.medical_certificate',
            FileUpload::TYPE_PHOTO => 'file_uploads.type.photo',
            FileUpload::TYPE_EXAM => 'file_uploads.type.exam',
            FileUpload::TYPE_SCHOOL_CERT => 'file_uploads.type.school_certificate',
            FileUpload::TYPE_INSURANCE => 'file_uploads.type.insurance',
            FileUpload::TYPE_PARENT_AGREEMENT => 'file_uploads.type.parent_agreement',
            FileUpload::TYPE_PREVIOUS_LICENCE => 'file_uploads.type.previous_licence',
            FileUpload::TYPE_SAFETY_PLAN => 'file_uploads.type.safety_plan',
            FileUpload::TYPE_ACCEPTANCE_ACT => 'file_uploads.type.acceptance_act',
            FileUpload::TYPE_OTHER => 'file_uploads.type.other',
            FileUpload::TYPE_COMP_RESULT => 'file_uploads.type.competition_result',
            FileUpload::TYPE_RECOMMENDATION => 'file_uploads.type.membership_recommendation',
            FileUpload::TYPE_REGISTRY => 'file_uploads.type.membership_registry',
            FileUpload::TYPE_ACTIVITY_DESC => 'file_uploads.type.activity_desc',
            FileUpload::TYPE_STATUTE_COPY => 'file_uploads.type.statute_copy',
        ];

        $statuses = [
            Pagination::$filterAny => '',
            FileUpload::STATUS_APPROVED => 'file_uploads.status.approved',
            FileUpload::STATUS_NEW => 'file_uploads.status.new',
            FileUpload::STATUS_REJECTED => 'file_uploads.status.rejected',
            FileUpload::STATUS_REVOKED => 'file_uploads.status.revoked',
        ];

        return [
            'files' => new Pagination($files, $request, [
                'sorters' => ['t.createdAt' => 'desc'],
                'applyFilter' => [$this, 'fileFilters'],
            ]),
            'types' => $types,
            'statuses' => $statuses,
        ];
    }

    /**
     * @Route("/{licenceId}/{id}/reject")
     * @ParamConverter("licence", class="AppBundle:Licence", options={"id" = "licenceId"})
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     * @param Licence $licence
     * @param FileUpload $file
     * @param Request $request
     * @return array
     */
    public function rejectAction(Licence $licence, FileUpload $file, Request $request)
    {
        if (!$licence->getDocuments()->contains($file)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new RejectType(), $file);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $file->setStatus(FileUpload::STATUS_REJECTED);
            if(!$licence->isDeclined()) {
                $licence->setStatus(Licence::STATUS_WAITING_EDIT);
            }
            $this->flush();

            $this->get('event_dispatcher')->dispatch('licence.status.changed', new StatusChangeEvent($licence, $file));

            $this->addFlash('success', $this->get('translator')->trans('file_uploads.flash.rejected'));
            return new JsonResponse([], 201);
        }

        return [
            'form' => $form->createView(),
            'file' => $file,
            'licence' => $licence
        ];
    }

    /**
     * @Route("/{licenceId}/{id}/edit")
     * @ParamConverter("licence", class="AppBundle:Licence", options={"id" = "licenceId"})
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN') or file.isOwner(user)")
     * @param Licence $licence
     * @param FileUpload $file
     * @param Request $request
     * @return array
     */
    public function editAction(Licence $licence, FileUpload $file, Request $request)
    {
        if ($file->isApproved()) {
            throw $this->createAccessDeniedException();
        }

        if (!$licence->getDocuments()->contains($file)) {
            throw $this->createNotFoundException();
        }

        $oldFile = clone $file;
        $form = $this->createForm(new DocumentType($licence->getUser(), false, $licence, false), $file);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $file->setStatus(FileUpload::STATUS_NEW);
            if (!$licence->hasRejectedFiles() && !$licence->isDeclined()) {
                $licence->setStatus(Licence::STATUS_WAITING_CONFIRM);
            }
            $this->flush();

            $this->get('app.document_handler')->removeDocument($oldFile);
            $this->get('event_dispatcher')->dispatch('licence.status.changed', new StatusChangeEvent($licence, $file));

            $this->addFlash('success', $this->get('translator')->trans('file_uploads.flash.edited'));
            return new JsonResponse([], 201);
        }

        return [
            'form' => $form->createView(),
            'file' => $file,
            'licence' => $licence
        ];
    }

    /**
     * @Route("/{licence}/{type}/new")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN') or licence.isOwner(user)")
     * @param Licence $licence
     * @param string $type
     * @param Request $request
     * @return array
     */
    public function newAction(Licence $licence, $type, Request $request)
    {
        $file = new FileUpload();
        $file->setType($type);
        $form = $this->createForm(new DocumentType($licence->getUser(), false, $licence,false), $file);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $file->setStatus(FileUpload::STATUS_NEW);
            if (!$licence->hasRejectedFiles() && !$licence->isDeclined()) {
                $licence->setStatus(Licence::STATUS_WAITING_CONFIRM);
            }

            if ($file->getType() == FileUpload::TYPE_INSURANCE) {
                $licence->setLasfInsurance(false);
            }

            $licence->addDocument($file);
            $licence->getUser()->addDocument($file);
            $this->persist($file);

            $this->flush();

            $this->get('event_dispatcher')->dispatch('licence.status.changed', new StatusChangeEvent($licence, $file));

            $this->addFlash('success', $this->get('translator')->trans('file_uploads.flash.edited'));
            return new JsonResponse([], 201);
        }

        return [
            'form' => $form->createView(),
            'file' => $file,
            'licence' => $licence
        ];
    }

    /**
     * @param QueryBuilder $qb
     * @param string $key
     * @param string $val
     */
    public function fileFilters(QueryBuilder $qb, $key, $val)
    {
        if (empty($val)) {
            return;
        }

        switch ($key) {
            case 't.fullName':
                $qb->andWhere($qb->expr()->like(
                    $qb->expr()->concat('u.firstname', $qb->expr()->concat($qb->expr()->literal(' '), 'u.lastname')),
                    ':uname'
                ));
                $qb->setParameter('uname', "%$val%");
                break;
            case 't.type':
                $qb->andWhere($qb->expr()->like('t.type', ':type'));
                $qb->setParameter('type', $val);
                break;
            case 't.status':
                $qb->andWhere($qb->expr()->like('t.status', ':s'));
                $qb->setParameter('s', $val);
                break;
        }
    }
}
