<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\filtre\recherche;
use App\Form\RechercheType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;

use App\Repository\SortieRepository;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\OrderBy;

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
    public function list (SortieRepository $sortieRepository ,CampusRepository $campusRepository, EtatRepository $etatRepository, Request $request):Response
    {
        $search= new recherche();
$searchForm=$this->createForm(RechercheType::class, $search);

$searchForm->handleRequest($request);

$user=$this->getUser();
//dump($user);


    $campusUser=$user->getCampus();

//dump ($campus);

        $sorties=$sortieRepository->findby(array('campus'=>$campusUser));

        $campuss=$campusRepository->findAll();



        return $this->render('sortie/liste.html.twig',[
            "sorties"=>$sorties, "campuss"=>$campuss, "searchForm"=>$searchForm->createView()
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

    }

    /**
     * @Route ("/sortie/sinscrire/{id}", name="sortie_sinscrire")
     */
    public function createParticipant(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        $user=$this->getUser();

        //dd($user);

        $sortie=$sortieRepository->find($id);
//dd($sortie);

      $sortie->addParticipant($user);
      $etat =$etatRepository->find(3);


      if ($sortie->getParticipants()->count() == $sortie->getNbInscriptionsMax()){
          $sortie->setEtat($etat);
      }

$entityManager->persist($sortie);
$entityManager->flush();



        return$this->redirectToRoute('sortie_liste');

    }

    /**
     * @Route ("/sortie/sedesister/{id}", name="sortie_desister")
     */
    public function deleteParticipant(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        $user=$this->getUser();

        //dd($user);

        $sortie=$sortieRepository->find($id);
//dd($sortie);

        $sortie->removeParticipant($user);

        $etat =$etatRepository->find(2);


        if ($sortie->getParticipants()->count() < $sortie->getNbInscriptionsMax()){
            $sortie->setEtat($etat);
        }

        $entityManager->persist($sortie);
        $entityManager->flush();



        return$this->redirectToRoute('sortie_liste');

    }

    /**
     * @Route ("/sortie/publier/{id}", name="sortie_publier")
     */
    public function publierSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {


        $sortie=$sortieRepository->find($id);
//dd($sortie);



        $etat =$etatRepository->find(2);



            $sortie->setEtat($etat);

        $entityManager->persist($sortie);
        $entityManager->flush();



        return$this->redirectToRoute('sortie_liste');

    }

//public function searchByCampus(Request $request, SortieRepository $sortieRepository):Response
//{
//
//          $campus= $searchForm->get('nom');
//        $sorties=$sortieRepository->findby(array('campus'=>$campus));
//}


}



