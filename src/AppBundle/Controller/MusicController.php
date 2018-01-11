<?php
namespace AppBundle\Controller;

use Symfony\Component\PropertyAccess\PropertyAccess;
use AppBundle\Entity\FavoriteSong;
use AppBundle\Entity\Music;
use AppBundle\Form\MusicType;
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
    use DoctrineController;

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
     * @Route("/favourite/{id}/{userId}")
     * @Method("GET")
     * @Template
     */
    public function favouriteAction($id, $userId){
        $songs = $this->get('em')->getRepository(FavoriteSong::class)->findAll();
        var_dump($songs);
        $exists = false;
        var_dump($songs[0].getId());
        foreach($songs as $song){
            if($song.getUserId()==$userId && $song.getSongId()==$id){
                $exists=true;
            }
        }
        if($exists){
            //delete the song from the database
            var_dump("EXISTS");
        } else {
            //add song to favorites list
            var_dump("DOESNT EXIST");
        }

        //return $this->redirectToRoute('music');
    }

    /**
     * @Route("/music-list")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template
     * @param Request $request
     * @return array
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
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addMusicAction(Request $request){

        $music = new Music();

        $form = $this->createForm(new MusicType(), $music);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return [
                'form' => $form->createView(),
                'music' => $music
            ];
        }

        $this->persist($music);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('sport.flash.created'));

        return $this->redirectToRoute('app_music_musiclist');
    }

    /**
     * @Route("/music-liked")
     * @Method("GET")
     * @Security("has_role('ROLE_USER') or has_role('ROLE_DECLARANT') or has_role('ROLE_RACER')")
     * @Template
     * @param Request $request
     * @return array
     */
    public function likedSongsAction(Request $request){
        $songs = $this->get('em')->getRepository(Music::class)->createQueryBuilder('m');
        return [
            'songs' => new Pagination($songs, $request)
        ];
    }

}
