<?php
namespace AppBundle\Controller;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\Music;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class MusicController extends Controller
{
    /**
     * @Route("/music", name="music")
     * @Method("GET")
     * @Template
     */
    public function indexAction(){

        $songs = $this->get('em')->getRepository(Music::class)->findAll();

        return  [
            'songs' => $songs
        ];
    }

    /**
     * @Route("/music/{song}", name="song")
     * @Method("GET")
     * @Template
     */
    public function songAction($song){

        return [
            'song' => $song
        ];
    }

}
