<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Music;
use DataDog\PagerBundle\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;

class MusicController extends Controller
{
    /**
     * @Route("/music")
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

    /**
     * @Route("/music-list")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function musicListAction(Request $request){

        $songs = $this->get('em')->getRepository(Music::class)->createQueryBuilder('m');

        return [
            'songs' => new Pagination($songs, $request)
        ];
    }

    /**
     * @Route("/add-music")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template
     */
    public function addMusicAction(){
        return [];
    }

}
