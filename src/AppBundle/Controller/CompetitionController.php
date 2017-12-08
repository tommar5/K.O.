<?php namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionJudge;
use AppBundle\Entity\CompetitionParticipant;
use AppBundle\Entity\FileUpload;
use AppBundle\Entity\User;
use AppBundle\Form\CompetitionJudgeType;
use AppBundle\Form\CompetitionType;
use AppBundle\Form\Type\Competition\ParticipantType;
use AppBundle\Form\Type\Competition\ResultFileType;
use AppBundle\Form\Type\User\TermsConfirmType;
use DataDog\PagerBundle\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/competition")
 */
class CompetitionController extends Controller implements VerifyTermsInterface, ChangePasswordInterface
{
    use DoctrineController;

    /**
     * @Route("")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_RACER') or has_role('ROLE_ORGANISATOR') or has_role('ROLE_JUDGE') or has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $competitions = $this->get('em')->getRepository(Competition::class)->createQueryBuilder('t');

        return [
            'competitions' => new Pagination($competitions, $request),
        ];
    }

    /**
     * @Route("/new")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ORGANISATOR')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $comp = new Competition();
        $form = $this->createForm(new CompetitionType($this->getUser()), $comp);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $comp->setUser($this->getUser());
            $this->persist($comp);
            $this->flush();
            $this->addFlash("success", $this->get('translator')->trans('competition.flash.created'));

            return $this->redirectToRoute('app_competition_index');
        }

        return [
            'entity' => $comp,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/{id}/edit")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_ORGANISATOR') and comp.isOwner(user))")
     *
     * @param Competition $comp
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Competition $comp, Request $request)
    {
        $form = $this->createForm(new CompetitionType($comp->getUser()), $comp);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->flush();
            $this->addFlash("success", $this->get('translator')->trans('competition.flash.updated'));

            return $this->redirectToRoute('app_competition_index');
        }

        return [
            'form' => $form->createView(),
            'entity' => $comp,
        ];
    }

    /**
     * @Route("/{id}/show")
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Competition $comp
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Competition $comp, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $judge = new CompetitionJudge();
        $form = $this->createForm(new CompetitionJudgeType($comp->getMainJudge()), $judge);
        $tempComp = new Competition();
        $resultForm = $this->createForm(new ResultFileType($tempComp), $tempComp);

        $canSubmitResults = $user->hasRole('ROLE_ORGANISATOR') && $comp->isOwner($user);
        $canEdit = $user->getId() == $comp->getMainJudge()->getId()
            || $user->hasRole('ROLE_ADMIN')
            || $canSubmitResults;

        if ($canSubmitResults) {
            $resultForm->handleRequest($request);

            if ($resultForm->isValid()) {
                foreach ($tempComp->getDocuments() as $doc) {
                    if (!$doc->getFile()) {
                        continue;
                    }
                    $doc->setStatus(FileUpload::STATUS_NEW);
                    $comp->getUser()->addDocument($doc);
                    $comp->addDocument($doc);
                    $this->persist($doc);
                }
                $this->flush();
                $this->addFlash("success", $this->get('translator')->trans('competition.flash.files_added'));

                return $this->redirectToRoute('app_competition_show', ['id' => $comp->getId()]);
            }
        }

        if ($canEdit) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $judge->setCompetition($comp);
                $this->persist($judge);
                $this->flush();
                $this->addFlash("success", $this->get('translator')->trans('competition.flash.judge_added'));

                return $this->redirectToRoute('app_competition_show', ['id' => $comp->getId()]);
            }
        }

        $results = $this->get('em')->getRepository(CompetitionParticipant::class)->createQueryBuilder('cp')
            ->join('cp.user', 'u')
            ->join('cp.competition', 'c')
            ->leftJoin('u.licences', 'ul')
            ->where('c = :competition')
            ->setParameter('competition', $comp)
            ->orderBy('cp.points', 'desc')
            ->getQuery()->getResult();

        return [
            'form' => $form->createView(),
            'resultForm' => $resultForm->createView(),
            'comp' => $comp,
            'results' => $results,
            'canEdit' => $canEdit,
            'canSubmitResults' => $canSubmitResults
        ];
    }

    /**
     * @Route("/{id}/result/add")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ORGANISATOR') and comp.isOwner(user)")
     * @Template
     * @param Competition $comp
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addResultAction(Competition $comp, Request $request)
    {
        $form = $this->createForm(new ParticipantType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var CompetitionParticipant $part */
            $part = $form->getData();
            $part->setCompetition($comp);

            $existing = $this->get('em')->getRepository(CompetitionParticipant::class)->findOneBy([
                'user' => $part->getUser(),
                'competition' => $part->getCompetition()
            ]);

            if ($existing) {
                $existing->setPoints($part->getPoints());
            } else {
                $this->get('em')->persist($part);
            }

            $this->flush();

            $this->addFlash('success', $this->get('translator')->trans('competition_participant.flash.results_saved'));
            return new JsonResponse([], 201);
        }

        return [
            'form' => $form->createView(),
            'comp' => $comp,
        ];
    }

    /**
     * @Route("/{id}/removeParticipant/{partId}")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ORGANISATOR') and comp.isOwner(user)")
     * @ParamConverter("part", class="AppBundle:CompetitionParticipant", options={"id" = "partId"})
     * @param Competition $comp
     * @param CompetitionParticipant $part
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeParticipantAction(Competition $comp, CompetitionParticipant $part)
    {
        if (!$comp->userParticipates($part->getUser())) {
            throw $this->createNotFoundException();
        }

        $comp->removeParticipant($part);
        $part->getUser()->removeRace($part);
        $this->remove($part);

        $this->addFlash('success', $this->get('translator')->trans('competition_participant.flash.participant_removed'));

        $this->flush();

        return $this->redirectToRoute('app_competition_show', ['id' => $comp->getId()]);
    }


    /**
     * @Route("/{id}/removeFile/{fileId}")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ORGANISATOR') and comp.isOwner(user)")
     * @ParamConverter("file", class="AppBundle:FileUpload", options={"id" = "fileId"})
     * @param Competition $comp
     * @param FileUpload $file
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeResultAction(Competition $comp, FileUpload $file)
    {
        if (!$comp->getDocuments()->contains($file)) {
            throw $this->createNotFoundException();
        }

        $comp->getUser()->removeDocument($file);
        $comp->removeDocument($file);
        $this->remove($file);

        $this->addFlash('success', $this->get('translator')->trans('competition.flash.file_removed'));

        $this->flush();

        return $this->redirectToRoute('app_competition_show', ['id' => $comp->getId()]);
    }

    /**
     * @Route("/judge/{id}/delete")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_ORGANISATOR')")
     *
     * @param CompetitionJudge $judge
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeJudgeAction(CompetitionJudge $judge)
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$judge->getCompetition()->isOwner($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $this->remove($judge);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('competition.flash.judge_removed'));

        return $this->redirectToRoute('app_competition_show', ['id' => $judge->getCompetition()->getId()]);
    }

    /**
     * @Route("/{id}/delete")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_ORGANISATOR') and comp.isOwner(user))")
     *
     * @param Competition $comp
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Competition $comp)
    {
        $this->remove($comp);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('competition.flash.removed'));

        return $this->redirectToRoute('app_competition_index');
    }

    /**
     * @Route("/{id}/join")
     * @Method({"GET"})
     * @Security("has_role('ROLE_RACER')")
     *
     * @param Competition $comp
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function joinAction(Competition $comp)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->participatesInCompetition($comp)) {
            $participation = new CompetitionParticipant();
            $participation->setUser($user);
            $participation->setCompetition($comp);
            $user->addRace($participation);
            $this->get('em')->persist($participation);
            $this->get('em')->flush();
            $this->addFlash('success', $this->get('translator')->trans('competition.flash.joined'));
        }

        return $this->redirectToRoute('app_competition_index');
    }

    /**
     * @Route("/events")
     * @Method({"GET"})
     * @Security("has_role('ROLE_JUDGE')")
     */
    public function getCalendarEvents()
    {
        $competitions = $this->get('em')->getRepository(Competition::class)->createQueryBuilder('c')
            ->leftJoin('c.judges', 'j')
            ->where('j.user = :judge')
            ->orWhere('c.mainJudge = :judge')
            ->setParameter('judge', $this->getUser())
            ->getQuery()->getResult();

        $eventData = [];
        /** @var Competition $comp */
        foreach ($competitions as $comp) {
            $eventData[] = [
                'title' => $comp->getName(),
                'start' => $comp->getDateFrom()->format(\DateTime::ISO8601),
                'end' => $comp->getDateTo() ? $comp->getDateTo()->format(\DateTime::ISO8601) : null,
                'url' => $this->generateUrl('app_competition_show', ['id' => $comp->getId()])
            ];
        }
        return new JsonResponse($eventData);
    }

    /**
     * @Route("/competition/agree-with-terms")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ORGANISATOR')")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function termsAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(new TermsConfirmType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if (!$user->isTermsConfirmed()) {
                $this->addFlash('info', $this->get('translator')->trans('competition.flash.please_agree'));
                return $this->redirect($this->generateUrl('app_competition_index'));
            }

            return $this->redirect($this->generateUrl('app_competition_new'));
        }

        return ['form' => $form->createView()];
    }
}
