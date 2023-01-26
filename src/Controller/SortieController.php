<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SerieRepository;
use App\Repository\SortieRepository;
use ContainerSejy5we\getCampusRepositoryService;
use Couchbase\UserManager;
use Doctrine\ORM\Mapping\OrderBy;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/liste", name="sortie_liste")
     */
    public function list (SortieRepository $sortieRepository ,CampusRepository $campusRepository):Response
    {


$user=$this->getUser();
//dump($user);

    $campusUser=$user->getCampus();

//dump ($campus);

        $sorties=$sortieRepository->findby(array('campus'=>$campusUser));

        $campuss=$campusRepository->findAll();


        return $this->render('sortie/liste.html.twig',[
            "sorties"=>$sorties, "campuss"=>$campuss
        ]);


    }

    /**
     * @Route ("/sortie/details/{id}", name="sortie_details")
     */

    public function details(int $id, SortieRepository$sortieRepository):Response
    {
        $sortie=$sortieRepository->find($id);
        if (!$sortie){
            throw $this->createNotFoundException('Erreur');
        }


        return $this->render('sortie/details.html.twig', [
            "sortie"=>$sortie
        ]);
        dump($sortie);
    }

}
