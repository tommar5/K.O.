<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\SubCompetition;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Comment controller.
 *
 */
class CommentController extends Controller
{
    use DoctrineController;

    /**
     * @Route("comment/{applicationId}/{type}/submit/{competition_id}", defaults={"competition_id" = null})
     * @ParamConverter("application", class="AppBundle:Application", options={"id" = "applicationId"})
     * @ParamConverter("subCompetition", class="AppBundle:SubCompetition", options={"id" = "competition_id"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @Security("is_granted('upload-comments', user)")
     * @param Application $application
     * @param string      $type
     * @param Request     $request
     * @param SubCompetition $subCompetition
     *
     * @return array|JsonResponse
     */
    public function commentAction(Application $application, $type, Request $request, SubCompetition $subCompetition = null)
    {
        $form = $this->createForm(new CommentType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment = new Comment();
            $comment->setType($type);
            $comment->setUser($this->getUser());
            $comment->setApplicationId($application->getId());
            if ($subCompetition) {
                $comment->setCompetitionId($subCompetition->getId());
            }
            $comment->setComment($form->getData()->getComment());
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            if ($comment->getComment()) {
                $this->get('app.application_document_handler')->commentApplicationDocuments($application, $comment);
            }
            $em->flush();

            return new JsonResponse([$comment->getComment()], 201);
        }

        return [
            'form' => $form->createView(),
            'application' => $application,
            'commentType' => $type,
            'competition' => $subCompetition,
        ];
    }

    /**
     * @Route("/comment/{comment}/delete")
     * @Method({"GET", "POST"})
     * @Security("is_granted('comment-delete', user)")
     * @param Comment $comment
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Comment $comment)
    {
        $applicationId = $comment->getApplicationId();

        $this->remove($comment);
        $this->flush();
        $this->addFlash('success', $this->get('translator')->trans('comment.flash.deleted'));

        return $this->redirectToRoute('app_application_modify', ['id' => $applicationId]);
    }
}
