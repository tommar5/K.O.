<?php
namespace AppBundle\Controller;

use AppBundle\Entity\FileUpload;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class MusicController extends Controller
{
    /**
     * @Route("/music")
     * @Method("GET")
     * @Template
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(){

        $songs = $this->get('em')->getRepository('AppBundle:Music')->createQueryBuilder('m')->
        orderBy('m.createdAt', 'ASC')->getQuery();

        return  [ 'songs' => $songs->execute()];
    }

    /**
     * @Route("/music/song/{song}", name="song")
     * @Method("GET")
     * @Template
     */
    public function songAction($song){

        return [
            'song' => $song
        ];
    }

}