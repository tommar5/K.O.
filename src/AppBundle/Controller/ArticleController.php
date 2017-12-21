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
     * @Route("/articles")
     * @Method("GET")
     * @Template
     */
    public function indexAction(){
        return [
            'articles' => []
        ];
    }

    /**
     * @Route("/articles/{id}", name="article")
     * @Method("GET")
     * @Template
     */
    public function articleAction(){

        return [];
    }
}
