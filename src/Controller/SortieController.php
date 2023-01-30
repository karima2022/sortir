<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\filtre\Recherche;
use App\Form\AnnulerSortieType;

use App\Form\RechercheType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;


use App\Repository\SortieRepository;


use App\Services\UpdateEtat;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/liste", name="sortie_liste")
     */
    public function list(UpdateEtat $updateEtat,SortieRepository $sortieRepository, EtatRepository $etatRepository,EntityManagerInterface $entityManager,CampusRepository $campusRepository, Request $request): Response
    {


      $updateEtat->UpdateEtat($sortieRepository,$etatRepository,$entityManager);

//        $sorties = $sortieRepository->findAll();
//        $etatCloture = $etatRepository->find(3);
//        $etatEnCours = $etatRepository->find(4);
//        $etatPasse = $etatRepository->find(5);
//        $etatCree = $etatRepository->find(1);
//        $etatOuvert = $etatRepository->find(2);
//
//        $time = new \DateTime();
//
//        $time->format('H:i:s Y-m-d');


        $search = new Recherche();
        $searchForm = $this->createForm(RechercheType::class, $search);

        $searchForm->handleRequest($request);
        $user = $this->getUser();
        if ($searchForm->isSubmitted()) {
            $campusSearch = $search->getCampus();
            $nomSearch = $search->getNom();
            $dateDebut = $search->getDateDebut();
            $dateFin = $search->getDateFin();
            $inscritSearch = $search->getSortieInscrit();
            $pasinscritSearch = $search->getSortieNonInscrit();
            $sortieOrganisateur = $search->getSortieOrganisateur();
            $sortiePassee = $search->getSortiePassee();
            $user = $this->getUser();

            // $sorties=$sortieRepository->findby(array('campus'=>$campusSearch));
            $sorties = $sortieRepository->filtre($search, $user);

//            foreach ($sorties as $sortie) {
//                // $heureFinSortie=$sortie->getDateHeureDebut()->add('+'.$sortie->getDuree.'minutes');
//                if ($sortie->getEtat() == $etatCree or $sortie->getEtat() == $etatOuvert) {
//                    if ($sortie->getDateHeureDebut() < $time) {
//                        $sortie->setEtat($etatPasse);
//                    } elseif ($sortie->getDateHeureDebut() == $time) {
//                        $sortie->setEtat($etatEnCours);
//                    } elseif ($sortie->getDateLimiteInscription() < $time) {
//                        $sortie->setEtat($etatCloture);
//                    }
//                }

//
//            }
//            $entityManager->persist($sortie);
//            $entityManager->flush();
        } else {
            $user = $this->getUser();

            $sorties = $sortieRepository->listeSortiesDefaut($user);



//            foreach ($sorties as $sortie) {
//                //    $heureFinSortie=$sortie->getDateHeureDebut()->date_add($sortie->getDuree());
//                // $heureFinSortie=date_diff($sortie->getDateHeureDebut(),$sortie->getDateHeureDebut()+$sortie->getDuree());
//                //  dd($heureFinSortie);
//                if ($sortie->getEtat() == $etatCree or $sortie->getEtat() == $etatOuvert) {
//                    if ($sortie->getDateHeureDebut() < $time) {
//                        $sortie->setEtat($etatPasse);
//                        // } elseif ($sortie->getDateHeureDebut()> $time && $sortie->getDateHeureDebut()->add('+'.$sortie->getDuree().'minutes')) {
//                        //   $sortie->setEtat($etatEnCours);
//                    } elseif ($sortie->getDateLimiteInscription() < $time) {
//                        $sortie->setEtat($etatCloture);
//                    }
                }


//            if($sortie->getDateHeureDebut()< ($time->modify('+'.$nbDay.' days'))){
//                $sortieRepository->remove($sortie, true);
////            }
//
//                $entityManager->persist($sortie);
//                $entityManager->flush();
//            }



        $campuss = $campusRepository->findAll();


        return $this->render('sortie/liste.html.twig', [
            "sorties" => $sorties, "campuss" => $campuss, "searchForm" => $searchForm->createView(),

        ]);


    }

    /**
     * @Route ("/sortie/details/{id}", name="sortie_details")
     */

    public function details(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Erreur');
        }


        return $this->render('sortie/details.html.twig', [
            "sortie" => $sortie
        ]);

    }

    /**
     * @Route ("/sortie/sinscrire/{id}", name="sortie_sinscrire")
     */
    public function createParticipant(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        $user = $this->getUser();

        $sortie = $sortieRepository->find($id);


        $sortie->addParticipant($user);
        $etat = $etatRepository->find(3);


        if ($sortie->getParticipants()->count() == $sortie->getNbInscriptionsMax()) {
            $sortie->setEtat($etat);
        }

        $entityManager->persist($sortie);
        $entityManager->flush();


        return $this->redirectToRoute('sortie_liste');

    }

    /**
     * @Route ("/sortie/sedesister/{id}", name="sortie_desister")
     */
    public function deleteParticipant(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        $user = $this->getUser();

        $sortie = $sortieRepository->find($id);


        $sortie->removeParticipant($user);

        $etat = $etatRepository->find(2);


        if ($sortie->getParticipants()->count() < $sortie->getNbInscriptionsMax()) {
            $sortie->setEtat($etat);
        }

        $entityManager->persist($sortie);
        $entityManager->flush();


        return $this->redirectToRoute('sortie_liste');

    }

    /**
     * @Route ("/sortie/publier/{id}", name="sortie_publier")
     */
    public function publierSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {


        $sortie = $sortieRepository->find($id);


        $etat = $etatRepository->find(2);


        $sortie->setEtat($etat);

        $entityManager->persist($sortie);
        $entityManager->flush();


        return $this->redirectToRoute('sortie_liste');

    }


    /**
     * @Route ("/sortie/annuler/{id}", name="sortie_annuler")
     */
    public function annulerSortie(int $id, Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatRepository $etatRepository)
    {
        $sortie = $sortieRepository->find($id);
        $etat = $etatRepository->find(6);


        $annulerSortieForm = $this->createForm(AnnulerSortieType::class, $sortie);
        $annulerSortieForm->handleRequest($request);


        if ($annulerSortieForm->isSubmitted() && $annulerSortieForm->isValid()) {

            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }



        return $this->render('sortie/annulerSortie.html.twig', [
            "annulerSortieForm" => $annulerSortieForm->createView(), "sortie" => $sortie

        ]);
    }


}



