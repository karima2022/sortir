<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Entity\Sortie;
use App\filtre\Recherche;
use App\Form\AnnulerSortieType;

use App\Form\RechercheType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;


use App\Repository\SortieRepository;


use App\Services\UpdateEtat;

use Doctrine\ORM\EntityManagerInterface;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/liste", name="sortie_liste")
     */
    public function list(UpdateEtat $updateEtat, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager, CampusRepository $campusRepository, Request $request): Response
    {


        $updateEtat->UpdateEtat($sortieRepository, $etatRepository, $entityManager);


        $search = new Recherche();
        $searchForm = $this->createForm(RechercheType::class, $search);

        $searchForm->handleRequest($request);
        /**
         * @var Participant $user
         */
        $user = $this->getUser();
        if ($searchForm->isSubmitted()) {

            $sorties = $sortieRepository->filtre($search, $user);

        } else {

            $sorties = $sortieRepository->listeSortiesDefaut($user);

        }

        $campuss = $campusRepository->findAll();


        return $this->render('sortie/liste.html.twig', [
            "sorties" => $sorties, "campuss" => $campuss, "searchForm" => $searchForm->createView(),

        ]);


    }


    /**
     * @Route ("/sortie/details/{id}", name="sortie_details")
     */

    public
    function details(int $id, SortieRepository $sortieRepository): Response
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
    public
    function createParticipant(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        /**
         * @var Participant $user
         */
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
    public
    function deleteParticipant(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        /**
         * @var Participant $user
         */
        $user = $this->getUser();

        $sortie = $sortieRepository->find($id);


        $sortie->removeParticipant($user);

        $etat = $etatRepository->findOneBy(array('libelle'=>'Ouverte'));


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
    public
    function publierSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {


        $sortie = $sortieRepository->find($id);


        $etat = $etatRepository->findOneBy(array('libelle'=>'Ouverte'));


        $sortie->setEtat($etat);

        $entityManager->persist($sortie);
        $entityManager->flush();


        return $this->redirectToRoute('sortie_liste');

    }


    /**
     * @Route ("/sortie/annuler/{id}", name="sortie_annuler")
     */
    public
    function annulerSortie(int $id, Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatRepository $etatRepository)
    {
        $sortie = $sortieRepository->find($id);
        $etat = $etatRepository->findOneBy(array('libelle'=>'Annulée'));


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


