<?php

namespace App\Controller;

use App\Entity\Participant;
use App\filtre\Recherche;
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

class SortieMobileController extends AbstractController
{
    /**
     * @Route("/sortieMobile/liste", name="sortieMobile_liste")
     */
    public function list(UpdateEtat $updateEtat, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager, CampusRepository $campusRepository, Request $request): Response
    {


        $updateEtat->UpdateEtat($sortieRepository, $etatRepository, $entityManager);
        /**
         * @var Participant $user
         */
            $user = $this->getUser();

            $sorties = $sortieRepository->MesSorties($user);



        $campuss = $campusRepository->findAll();


        return $this->render('sortieMobile/liste.html.twig', [
            "sorties" => $sorties, "campuss" => $campuss,

        ]);


    }


    /**
     * @Route ("/sortieMobile/details/{id}", name="sortieMobile_details")
     */

    public
    function details(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Erreur');
        }


        return $this->render('sortieMobile/details.html.twig', [
            "sortie" => $sortie
        ]);

    }

}
