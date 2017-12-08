<?php namespace AppBundle\Controller;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use AppBundle\Event\StatusChangeEvent;
use AppBundle\Event\UserCreateEvent;
use AppBundle\Form\LicenceType;
use AppBundle\Form\Type\Licence\ExtendType;
use AppBundle\Form\Type\Licence\InfoType;
use AppBundle\Form\Type\Licence\RejectType;
use AppBundle\Form\Type\Licence\TypeSelectType;
use AppBundle\Form\Type\User\NotesType;
use DataDog\PagerBundle\Pagination;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LicencesController extends Controller implements VerifyTermsInterface, ChangePasswordInterface
{
    use DoctrineController;

    /**
     * @Route("/licences")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_ACCOUNTANT')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $filterService = $this->get('app.filter.type.service');
        $statuses = $filterService->getAllLicenceStatuses();

        $licences = $this->get('em')->getRepository(Licence::class)->createQueryBuilder('l')
            ->join('l.user', 'u')
            ->where('l.status <> :status')
            ->setParameter('status', Licence::STATUS_UNCONFIRMED);

        if (!$this->getUser()->hasRole('ROLE_ADMIN') && $this->getUser()->hasRole('ROLE_ACCOUNTANT')) {
            $licences->andWhere('l.status IN (:accountant)')->setParameter('accountant', [Licence::STATUS_NOT_PAID, Licence::STATUS_PAID, Licence::STATUS_INVOICE]);
            $statuses = $filterService->getAccountantLicenceStatuses();
        }

        $licenceTypes = (new TypeSelectType($this->getUser()))->getTypesForFilter();

        return [
            'licences' => new Pagination($licences, $request, [
                'sorters' => ['l.updatedAt' => 'desc'],
                'applyFilter' => [$this, 'licenceFilters'],
                'applySorter' => [$this, 'licenceSorters'],
            ]),
            'types' => $licenceTypes,
            'statuses' => $statuses,
            'genderTypes' => $filterService->getGenderTypes(),
            'driverTypes' => $filterService->getDriverTypes(),
            'languageTypes' => $filterService->getLanguageTypes(),
            'sportTypes' => $filterService->getSportsType(),
        ];
    }

    /**
     * @Route("/my-licences")
     * @Method("GET")
     * @Template
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myAction()
    {
        $lpExpressList = $this->get('app.lasf.lp_express.service')->getLpExpressList();

        $licences = $this->get('em')->getRepository(Licence::class)->createQueryBuilder('t')
            ->addSelect('td', 'tr', 'ts')
            ->leftJoin('t.documents', 'td')
            ->leftJoin('t.representatives', 'tr')
            ->leftJoin('t.sports', 'ts')
            ->where('t.status <> :status')
            ->andWhere('t.user = :user')
            ->orderBy('t.id', 'desc')
            ->setParameter('user', $this->getUser())
            ->setParameter('status', Licence::STATUS_UNCONFIRMED)
            ->getQuery()->getResult();

        return [
            'licences' => $licences,
            'lpExpressList' => $lpExpressList,
        ];
    }

    /**
     * @Route("/user-licences/{id}")
     * @Method("GET")
     * @Security("has_role('ROLE_DECLARANT') and person.isParent(user)")
     * @Template
     *
     * @param User $person
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userAction(User $person)
    {
        $lpExpressList = $this->get('app.lasf.lp_express.service')->getLpExpressList();

        $licences = $this->get('em')->getRepository(Licence::class)->createQueryBuilder('t')
            ->where('t.status <> :status')
            ->andWhere('t.user = :user')
            ->orderBy('t.id', 'desc')
            ->setParameter('user', $person)
            ->setParameter('status', Licence::STATUS_UNCONFIRMED)
            ->getQuery()->getResult();

        return [
            'licences' => $licences,
            'user' => $person,
            'lpExpressList' => $lpExpressList,
        ];
    }

    /**
     * @Route("/licences/new/{id}", defaults={"id" = 0})
     * @Method({"GET", "POST"})
     * @Template
     * @param Request $request
     * @param $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request, $id)
    {
        $me = $this->getUser();
        $user = $id == 0 ? $me : $this->get('em')->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        if ($me->getId() != $user->getId() && !$user->isParent($me)) {
            throw $this->createAccessDeniedException();
        }

        $prefilled = null;
        if ($user->getPrefLicence()) {
            $prefilled = new Licence();
            $prefilled->setType($user->getPrefLicence());
        }

        $form = $this->createForm(new TypeSelectType($user), $prefilled);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Licence $licence */
            $licence = $form->getData();

            $oldLicence = $user->getUnfinishedLicence($licence->getType());
            $licence = $oldLicence ? $oldLicence : $licence;
            $licence->setCreateAt(new \DateTime());

            $licence->setUser($user);

            if (!$user->isLegal() && !$user->getBirthday()) {
                $this->addFlash('warning', $this->get('translator')->trans('licences.flash.birthday'));
                if ($licence->isOwner($me)) {
                    return $this->redirectToRoute('app_user_profile');
                } else {
                    return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
                }
            }

            $licence->setExpiresAt(new \DateTime(date('Y-12-31')));
            $licence->setDeliverTo($user->getAddress());
            $this->persist($licence);

            if ($user->getPrefLicence() == $licence->getType()) {
                $user->setPrefLicence(null);
            }

            $this->flush();

            $this->get('event_dispatcher')->dispatch('licence.status.changed', new StatusChangeEvent($licence));

            $this->addFlash('info', $this->get('translator')->trans('licences.flash.upload_info'));
            return $this->redirect($this->generateUrl('app_licences_info', ['id' => $licence->getId()]));
        }

        return ['form' => $form->createView(), 'id' => $id];
    }

    /**
     * @Route("/licences/{id}/info")
     * @Method({"GET", "POST"})
     * @Security("licence.isOwner(user) or (has_role('ROLE_DECLARANT') and licence.getUser().isParent(user))")
     * @Template
     * @param Licence $licence
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function infoAction(Licence $licence, Request $request)
    {
        if ($licence->isCancelled() || $licence->isCompleted()) {
            throw $this->createNotFoundException();
        }

        $user = $licence->getUser();

        $this->prefillLicence($licence);

        $lpExpressList = $this->get('app.lasf.lp_express.service')->getLpExpressList();
        $form = $this->createForm(new InfoType($user, $licence, $this->get('app.urgency_choices')), $licence, ['allow_extra_fields' => $lpExpressList,]);

        if ($licence->isMembershipLicence()) {
            $this->setMembershipData($licence, $form);
        }

        $form->handleRequest($request);

        if ($form->isValid()) {

            foreach ($licence->getDocuments() as $doc) {
                if (!$doc->getFile()) {
                    $licence->removeDocument($doc);
                    continue;
                }
                $doc->setStatus(FileUpload::STATUS_NEW);
                $licence->getUser()->addDocument($doc);
                $this->persist($doc);
            }

            $this->get('app.licence_document_handler')->handleLicenceDocuments($licence, $form);

            $licence->setStatus(Licence::STATUS_UPLOADED);
            $this->persistLicense($licence, $user, $form);
            $this->flush();
            $this->get('event_dispatcher')->dispatch('licence.status.changed', new StatusChangeEvent($licence));

            $this->addFlash('success', $this->get('translator')->trans('licences.flash.created'));

            return $this->redirect(
                $user->getId() == $this->getUser()->getId()
                    ? $this->generateUrl('app_licences_my')
                    : $this->generateUrl('app_licences_user', ['id' => $user->getId()])
            );
        }

        return [
            'form' => $form->createView(),
            'licence' => $licence
        ];
    }

    /**
     * @Route("/licences/{id}/extend")
     * @Method({"GET", "POST"})
     * @Security("licence.isOwner(user, licence)")
     * @Template
     * @param Licence $licence
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function extendAction(Licence $licence, Request $request)
    {
        if (!$licence->getCanBeExtended()) {
            throw $this->createNotFoundException();
        }

        $tempLicence = new Licence();
        $tempLicence->setType($licence->getType());
        $tempLicence->setSports($licence->getSports());

        $lpExpressList = $this->get('app.lasf.lp_express.service')->getLpExpressList();
        $form = $this->createForm(new ExtendType($this->getUser(), $tempLicence, $this->get('app.urgency_choices')), $tempLicence, ['allow_extra_fields' => $lpExpressList,]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            foreach ($tempLicence->getDocuments() as $doc) {
                if (!$doc->getFile()) {
                    continue;
                }

                $doc->setStatus(FileUpload::STATUS_NEW);
                $licence->getUser()->addDocument($doc);
                $licence->addDocument($doc);
                $this->persist($doc);
            }

            $this->get('app.licence_document_handler')->handleLicenceDocuments($licence, $form);

            $licence->setStatus(Licence::STATUS_EXTEND);
            $licence->setExtending(true);
            $licence->setLasfInsurance($tempLicence->isLasfInsurance());
            $licence->setIdentityCode($tempLicence->getIdentityCode());
            $licence->setUrgency($tempLicence->getUrgency());
            $licence->setdeliverto($tempLicence->getDeliverTo());
            $licence->setDeliverToAddress($tempLicence->getDeliverToAddress());

            $date = date('Y-12-31');
            if (date('m') == '12') {
                $date .='+1 year';
            }
            $licence->setExpiresAt(new \DateTime($date));
            $licence->setSeries(null);
            $licence->setSerialNumber(null);
            $this->flush();

            $this->get('event_dispatcher')->dispatch('licence.status.changed', new StatusChangeEvent($licence));

            $this->addFlash('success', $this->get('translator')->trans('licences.flash.extend_submitted'));
            return $this->redirect($this->generateUrl('app_licences_my'));
        }

        return [
            'form' => $form->createView(),
            'licence' => $licence,
        ];
    }

    /**
     * @Route("/licences/{id}/edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_ACCOUNTANT')")
     * @Template
     * @param Licence $licence
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Licence $licence, Request $request)
    {

        $licences = $this->get('em')->getRepository(Licence::class)->createQueryBuilder('l')
            ->addSelect('lb')
            ->join('l.user', 'u')
            ->leftJoin('l.basedOnLicence', 'lb')
            ->where('u.enabled = 1')
            ->andWhere('l.id = :id')
            ->andWhere('l.expiresAt >= :now')
            ->andWhere('l.status IN(:status)')
            ->andWhere('u = :user')
            ->orderBy('l.expiresAt', 'desc')
            ->setParameter('id', $licence->getId())
            ->setParameter('now', (new \DateTime())->format('Y-m-d'))
            ->setParameter('status', Licence::$completedStatuses)
            ->setParameter('user', $licence->getUser())
            ->getQuery()->getResult();

        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return [
                'licence' => $licence,
                'licences' => $licences,
            ];
        }

        $form = $this->createForm(new LicenceType($licence->getUser(), $licence), $licence);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if (!is_null($licence->getPersonalCode()) && $licence->isMembershipLicence()) {
                $licence->getUser()->setAssociated($licence->getPersonalCode());
            }
            foreach ($licence->getDocuments() as $doc) {
                if (!$doc->getFile() && !$doc->getId()) {
                    $licence->removeDocument($doc);
                    continue;
                }

                $doc->setStatus(FileUpload::STATUS_NEW);
                $licence->getUser()->addDocument($doc);
                $this->persist($doc);
            }

            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('licences.flash.updated'));
            return $this->redirect($this->generateUrl('app_licences_edit', ['id' => $licence->getId()]));
        }

        foreach ($licence->getDocuments() as $doc) {
            if (!$doc->getId()) {
                $licence->removeDocument($doc);
            }
        }

        $notesForm = $this->createForm(new NotesType(), $licence->getUser());
        $notesForm->handleRequest($request);

        if ($notesForm->isValid()) {
            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('licences.flash.notes_updated'));
            return $this->redirect($this->generateUrl('app_licences_edit', ['id' => $licence->getId()]));
        }

        $qb = $this->get('em')->getRepository("DataDogAuditBundle:AuditLog")->createQueryBuilder('a')
            ->addSelect('s', 't', 'b')
            ->innerJoin('a.source', 's')
            ->leftJoin('a.target', 't')
            ->leftJoin('a.blame', 'b')
            ->where('s.fk = :fk')
            ->andWhere('s.class = :class')
            ->orderBy('a.id', 'desc')
            ->setParameters([
                'fk' => $licence->getId(),
                'class' => Licence::class
            ]);

        $lpExpressList = $this->get('app.lasf.lp_express.service')->getLpExpressList();
        $logs = new Pagination($qb, $request);

        return [
            'form' => $form->createView(),
            'notesForm' => $notesForm->createView(),
            'licence' => $licence,
            'logs' => $logs,
            'licences' => $licences,
            'lpExpressList' => $lpExpressList,
        ];
    }

    /**
     * @Route("/licences/{id}/delete")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Template
     * @param Licence $licence
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Licence $licence)
    {

        // Get Licence Relations to other Licences
        $childLicences = $this->get('em')->getRepository(Licence::class)->createQueryBuilder('l')
            ->addSelect('lb')
            ->join('l.user', 'u')
            ->leftJoin('l.basedOnLicence', 'lb')
            ->where('u.enabled = 1')
            ->andWhere('l.id = :id')
            ->andWhere('l.expiresAt >= :now')
            ->andWhere('u = :user')
            ->orderBy('l.expiresAt', 'desc')
            ->setParameter('id', $licence->getId())
            ->setParameter('now', (new \DateTime())->format('Y-m-d'))
            ->setParameter('user', $licence->getUser())
            ->getQuery()->getResult();

        foreach ($childLicences as $childLicence) {
            if($childLicence->getBasedOnLicence()){
                foreach ($childLicence->getBasedOnLicence() as $isExist) {
                    if ($isExist->getId()) {

                        $this->addFlash('danger', $this->get('translator')->trans('licences.flash.error_deleting_relation_exists').($isExist->getId()));

                        return $this->redirect($this->generateUrl('app_licences_index'));
                    }
                }
            }
        }

        if (!$licence->isOrganisatorLicence()) {
            foreach ($licence->getRepresentatives() as $representative) {
                $licence->removeRepresentative($representative);
                $this->remove($representative);
            }
            $this->remove($licence);
            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('licences.flash.deleted'));
        } else {
            $this->addFlash('danger', $this->get('translator')->trans('licences.flash.error_deleting'));
        }

        return $this->redirect($this->generateUrl('app_licences_index'));
    }

    /**
     * @Route("/licences/{id}/modify")
     * @Method({"GET", "POST"})
     * @Security("licence.isOwner(user, licence)")
     * @Template
     * @param Licence $licence
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function modifyAction(Licence $licence, Request $request)
    {
        $user = $licence->getUser();
        $this->prefillLicence($licence);

        $lpExpressList = $this->get('app.lasf.lp_express.service')->getLpExpressList();
        $form = $this->createForm(new InfoType($user, $licence, $this->get('app.urgency_choices'), true), $licence, ['allow_extra_fields' => $lpExpressList,]);

        if ($licence->isMembershipLicence()) {
            $this->setMembershipData($licence, $form);
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            if(!$licence->hasRejectedFiles()) {
                $licence->setStatus(Licence::STATUS_WAITING_CONFIRM);
            }

            $licence->setReason(null);
            $this->persistLicense($licence, $user, $form);
            $this->persist($licence);
            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('licences.flash.updated'));
            return $this->redirect($this->generateUrl('app_licences_my'));
        }

        return [
            'form' => $form->createView(),
            'licence' => $licence,
        ];
    }

    /**
     * @Route("/licences/{id}/confirm")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     * @param Licence $licence
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmAction(Licence $licence)
    {
        if (!$licence->isConfirmable()) {
            throw $this->createNotFoundException();
        }

        $licence->setStatus(Licence::STATUS_NOT_PAID);
        foreach ($licence->getDocuments() as $document) {
            $document->setStatus(FileUpload::STATUS_APPROVED);

            if ($document->getType() == FileUpload::TYPE_PHOTO && !$licence->getUser()->getImageName()) {
                $helper = $this->container->get('vich_uploader.storage.file_system');
                $path = $helper->resolvePath($document, 'file');
                $file = new File($path);
                $name = uniqid() .'.'. $file->getExtension();
                $this->get('filesystem')->copy($path, $this->getParameter('kernel.root_dir') . '/../httpdocs/images/' . $name);
                $document->getUser()->setImageName($name);
            }
        }

        if ($licence->isMembershipLicence()) {
            $licence->getUser()->setAssociated($licence->getPersonalCode());
        }

        $this->flush();

        $this->get('event_dispatcher')->dispatch('licence.status.changed', new StatusChangeEvent($licence));

        return $this->redirectToRoute('app_licences_edit', ['id' => $licence->getId()]);
    }

    /**
     * @Route("/licences/{id}/decline")
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     * @param Licence $licence
     * @param Request $request
     * @return array
     */
    public function declineAction(Licence $licence, Request $request)
    {
        if (!$licence->isConfirmable()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new RejectType(), $licence);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $licence->setStatus(Licence::STATUS_DECLINED);
            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('licences.flash.declined'));
            return new JsonResponse([], 201);
        }

        return [
            'form' => $form->createView(),
            'licence' => $licence
        ];
    }

    /**
     * @Route("/licences/{id}/mark_as_paid")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTANT')")
     * @param Licence $licence
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function markAsPaid(Licence $licence)
    {
        if (!$licence->isNotPaid() && !$licence->isInvoiceSent()) {
            throw $this->createNotFoundException();
        }

        $licence->setStatus(Licence::STATUS_PAID);

        $this->get('lasf.licence_serial_code')->createSerialNumber($licence);

        $this->flush();

        $this->get('event_dispatcher')->dispatch('licence.status.changed', new StatusChangeEvent($licence));
        $this->addFlash('success', $this->get('translator')->trans('licences.flash.paid'));

        return $this->redirectToRoute('app_licences_edit', ['id' => $licence->getId()]);
    }

    /**
     * @Route("/licences/{id}/mark_as_produced")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Licence $licence
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function markAsProduced(Licence $licence)
    {
        if (!$licence->isPaid()) {
            throw $this->createNotFoundException();
        }

        $licence->setStatus(Licence::STATUS_PRODUCED);
        $licence->setExtending(false);

        $this->addFlash('success', $this->get('translator')->trans('licences.flash.produced'));

        $this->flush();

        $this->get('event_dispatcher')->dispatch('licence.status.changed', new StatusChangeEvent($licence));

        return $this->redirectToRoute('app_licences_edit', ['id' => $licence->getId()]);
    }

    /**
     * @Route("/licences/{id}/mark_as_invoice")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTANT')")
     * @param Licence $licence
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function markAsInvoice(Licence $licence)
    {
        if (!$licence->isNotPaid()) {
            throw $this->createNotFoundException();
        }

        $licence->setStatus(Licence::STATUS_INVOICE);

        $this->addFlash('success', $this->get('translator')->trans('licences.flash.invoice'));

        $this->flush();

        $this->get('event_dispatcher')->dispatch('licence.status.changed', new StatusChangeEvent($licence));

        return $this->redirectToRoute('app_licences_edit', ['id' => $licence->getId()]);
    }

    /**
     * @Route("/licences/{id}/mark_as_not_paid")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTANT')")
     * @param Licence $licence
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function markAsNotPaid(Licence $licence)
    {
        if (!$licence->isPaid() && !$licence->isInvoiceSent()) {
            throw $this->createNotFoundException();
        }

        $licence->setStatus(Licence::STATUS_NOT_PAID);

        $licence->setSeries(null);
        $licence->setSerialNumber(null);

        $this->flush();

        $this->addFlash('success', $this->get('translator')->trans('licences.flash.not_paid'));

        return $this->redirectToRoute('app_licences_edit', ['id' => $licence->getId()]);
    }

    /**
     * @Route("/licences/{id}/remind")
     * @Method("GET")
     * @Security("has_role('ROLE_ACCOUNTANT')")
     * @param Licence $licence
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remindAction(Licence $licence)
    {
        if (!$licence->isNotPaid() && !$licence->isInvoiceSent()) {
            throw $this->createNotFoundException();
        }

        $this->get('mail')->user($licence->getUser(), 'remind_not_paid', [
            'licence' => $licence->getType()
        ]);

        $this->addFlash('success', $this->get('translator')->trans('licences.flash.remind_not_paid'));

        return $this->redirectToRoute('app_licences_edit', ['id' => $licence->getId()]);
    }

    public function licenceSorters(QueryBuilder $qb, $key, $direction)
    {
        switch ($key) {
            case 'fullName':
                $qb->orderBy('CONCAT(u.firstname, u.lastname)', $direction);
                break;
            case 'series':
                $qb->orderBy('CONCAT(l.series, l.serialNumber)', $direction);
                break;
        }
    }


    /**
     * @Route("/licences/{id}/remove-document/{document}")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_ACCOUNTANT') or licence.isOwner(user)")
     * @param Licence $licence
     * @param FileUpload $document
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeDocument(Licence $licence, FileUpload $document)
    {
        if ($licence->getDocuments()->contains($document)) {
            $licence->removeDocument($document);
            $oldFile = clone $document;
            $this->remove($document);
            $this->flush();
            $this->get('app.document_handler')->removeDocument($oldFile);
            $this->addFlash('success', $this->get('translator')->trans('licences.flash.document_removed'));
        } else {
            $this->addFlash('danger', $this->get('translator')->trans('licences.flash.document_not_removed'));
        }
        return $this->redirect($this->container->get('request')->headers->get('referer'));
    }

    /**
     * Generate Licences CSV file
     * @Route("/licences/get-csv")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_ACCOUNTANT')")
     * @param Request $request
     * @return StreamedResponse
     */
    public function getCSVAction(Request $request)
    {
        $filters = $request->query->all();

        $response = new StreamedResponse();
        $response->setCallback(function() use ($filters){
            return $this->get('app.licences_csv_generator.service')->licenceCSVgenerator($filters, $this->getUser());
        });
        $response->headers->set('Content-Transfer-Encoding:', 'UTF-8');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="licences_csv.csv"');

        return $response;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $key
     * @param string $val
     */
    public function licenceFilters(QueryBuilder $qb, $key, $val)
    {
        $this->get('app.filter_licences.service')->licencesFilter($qb, $key, $val);
    }

    private function prefillLicence(Licence $licence)
    {
        $user = $licence->getUser();

        if ($licence->isOrganisatorLicence()) {
            $licence->setLasfAddress($user->getAddress());
            $licence->setLasfName($user->getMemberName());
            $licence->setPhone($user->getPhone());
            $licence->setVatNumber($user->getVatCode());
            $licence->setBank($user->getBank());
            $licence->setBankAccount($user->getBankAccount());
        }

        if ($licence->isDeclarantLicence()) {
            $licence->setLasfName($user->getMemberName());
            $licence->setLasfAddress($user->getAddress());
            $licence->setVatNumber($user->getVatCode());
            $licence->setPersonalCode($user->getMemberCode());
            $licence->setEmail($user->getEmail());
            $licence->setMobileNumber($user->getPhone());
            $licence->setManagerFullName($user->getFullName());
            $licence->setCity($user->getCity());
        }

        if ($licence->isDriverLicence()) {
            $licence->setEmail($user->getEmail());
            $licence->setGender($user->getGender());
            $licence->setCity($user->getCity());
            $licence->setGender($user->getGender());
            $user->getPhone() ? $licence->setMobileNumber($user->getPhone()) : $user->setPhone($licence->getMobileNumber());
            $licence->setLanguages($user->getLanguages());
            $licence->setSecondaryLanguage($user->getSecondaryLanguage());
            $licence->setDate($user->getBirthday());
            $licence->setCity($user->getCity());
        }

        if ($licence->isJudgeLicence()) {
            $licence->setGender($user->getGender());
            $licence->setCity($user->getCity());
            $licence->setGender($user->getGender());
            $licence->setName($user->getFullName());
            if ($user->getBirthday()) {
                $licence->setDate($user->getBirthday());
            }
            $user->getAddress() ? $licence->setLasfAddress($user->getAddress()) : $user->setAddress($licence->getLasfAddress());
            $user->getPhone() ? $licence->setMobileNumber($user->getPhone()) : $user->setPhone($licence->getMobileNumber());
            $licence->setEmail($user->getEmail());
            $licence->setLanguages($user->getLanguages());
            $licence->setSecondaryLanguage($user->getSecondaryLanguage());
            $licence->setDate($user->getBirthday());
            $licence->setCity($user->getCity());
        }
    }

    private function setMembershipData(Licence $licence, Form $form)
    {
        $user = $licence->getUser();

        $form->get('fullName')->setData($user->getFullName());
        $form->get('bank')->setData($user->getBank());
        $form->get('bankAccount')->setData($user->getBankAccount());
        $form->get('memberName')->setData($user->getMemberName());
        $form->get('memberCode')->setData($user->getMemberCode());
        $form->get('vatCode')->setData($user->getVatCode());
        $form->get('personalCode')->setData($user->isAssociated());
        $form->get('address')->setData($user->getAddress());
        $form->get('phone')->setData($user->getPhone());
    }

    private function setUserMembershipData(User $user, Form $form)
    {
        $user->setBank($form->get('bank')->getData());
        $user->setBankAccount($form->get('bankAccount')->getData());
        $user->setMemberName($form->get('memberName')->getData());
        $user->setMemberCode($form->get('memberCode')->getData());
        $user->setVatCode($form->get('vatCode')->getData());
        $user->setAddress($form->get('address')->getData());
        $user->setPhone($form->get('phone')->getData());
    }

    /**
     * @param Licence $licence
     * @param User $user
     * @param Form $form
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function persistLicense(Licence $licence, User $user, Form $form)
    {
        if ($licence->isMembershipLicence()) {
            $this->setUserMembershipData($user, $form);
        }

        if ($licence->isJudgeLicence() || $licence->isDriverLicence()) {
            $user->setLanguages($licence->getLanguages());
            $user->setSecondaryLanguage($licence->getSecondaryLanguage());
            $user->setCity($licence->getCity());

            foreach ($licence->getSports() as $sport) {
                $this->persist($sport);
            }
        }
        
        if ($licence->isDeclarantLicence()) {
            $userRepo = $this->get('em')->getRepository(User::class);

            foreach ($licence->getRepresentatives() as $rep) {
                $this->persist($rep);
            }

            $registrator = $this->get('app.registrator');
            $registeredEmails = [];

            /** @var User $racer */
            foreach ($form->get('users')->getData() as $racer) {
                if ($existingRacer = $userRepo->findOneBy(['email' => $racer->getEmail()])) {
                    $existingRacer->addRole('ROLE_RACER');
                    $existingRacer->setPrefLicence($racer->getPrefLicence());
                    $this->getUser()->addMember($existingRacer);
                } else {
                    if (in_array($racer->getEmail(), $registeredEmails)) {
                        continue;
                    }
                    $racer->addRole('ROLE_RACER');
                    $racer->setPlainPassword($registrator->createRandomString(8));
                    $registrator->registerUser($this->getUser(), $racer);
                    $this->get('event_dispatcher')->dispatch('user.created', new UserCreateEvent($racer));
                    $this->persist($racer);
                    $registeredEmails[] = $racer->getEmail();
                }
            }
        }
    }
}
