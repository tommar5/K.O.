<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Article;
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

        $articles = $this->get('em')->getRepository(Article::class)->findAll();

        return  [
            'articles' => $articles
        ];

    }

    /**
     * @Route("/article/article/{article}", name="article")
     * @Method("GET")
     * @Template
     */
    public function articleAction($article){

        $articles = $this->get('em')->getRepository(Article::class)->findOneByTitle($article);

        return [
            'article' => $articles
        ];
    }
}