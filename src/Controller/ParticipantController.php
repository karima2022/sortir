<?php

namespace App\Controller;

use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;



class ParticipantController extends AbstractController
{
    /**
     * @Route("/profile", name="profile_main")
     */

    public function afficherProfile(Request $request,EntityManagerInterface $entityManager,
                                    UserPasswordHasherInterface $userPasswordHasher  )
    {
        $participant = $this->getUser();
        $participantForm = $this->createForm(ParticipantType::class,$participant);
        $participantForm->handleRequest($request);

        if($participantForm->isSubmitted() && $participantForm->isValid())
        {
            $participant->setMotPasse(
                $userPasswordHasher->hashPassword(
                    $participant,
                    //la comme vous voyez ,on fait le hachage de plainMotPasse la confirmation de motPasse
                    //remarque :le changement de nom ne change rien dans la BDD ,il reste toujours motPasse dans la BDD
                    //et on va bien avoir le motPasse haché dans notre BDD
                    $participantForm->get('plainMotPasse')->getData()
                )
            );
            $entityManager->persist($participant);
            $entityManager->flush();

        }
        return $this->render('sortie/profile.html.twig', [
            'participantForm' => $participantForm->createView()
        ]);
    }
}