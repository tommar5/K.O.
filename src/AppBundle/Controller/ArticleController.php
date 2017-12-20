<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    /**
     * @Route("/article", name="articles")
     * @Method("GET")
     * @Template
     */
    public function indexAction(){

        return [];
    }

    /**
     * @Route("/article/article/{article}", name="article")
     * @Method("GET")
     * @Template
     */
    public function articleAction($article){

        return [
            'article' => $article
        ];
    }
}