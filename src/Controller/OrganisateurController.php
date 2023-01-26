<?php

namespace App\Controller;



use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OrganisateurController extends AbstractController
{
    /**
     * @Route("/profilorg/{id}", name="profilOrganisateur_main")
     */
    public function afficherProfilOrganisateur(int $id,ParticipantRepository $participantRepository)
    {
        $profil = $participantRepository->find($id);

        if(!$profil)
        {
            throw $this->createNotFoundException('Cet organisateur n\'existe pas !! ');
        }
        return $this->render('sortie/profileOrganisateur.html.twig',[
                'profilOrganisateur' => $profil
            ]

        );

    }

}