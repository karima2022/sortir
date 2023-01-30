<?php

namespace App\Services;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;


class UpdateEtat
{
    public function UpdateEtat(SortieRepository $sortieRepository, EtatRepository $etatRepository,EntityManagerInterface $entityManager)
    {

        $etatCloture = $etatRepository->findOneBy(array('libelle' => 'Clôturée'));
        $etatEnCours = $etatRepository->findOneBy(array('libelle' => 'Activité en cours'));
        $etatPasse = $etatRepository->findOneBy(array('libelle' => 'Passée'));
        $etatCree = $etatRepository->findOneBy(array('libelle' => 'Créée'));
        $etatOuvert = $etatRepository->findOneBy(array('libelle' => 'Ouverte'));

        $time = new \DateTime();

        $time->format('H:i:s Y-m-d');

        $sortiesOuvertes = $sortieRepository->findby(array('etat' => $etatOuvert));
        $sortiesCreees= $sortieRepository->findby(array('etat' => $etatCree));
        $sortiesCloturees= $sortieRepository->findby(array('etat' => $etatCloture));
        $sortiesEnCours= $sortieRepository->findby(array('etat' => $etatEnCours));


        foreach ($sortiesOuvertes as $sortie) {
            if ($sortie->getDateLimiteInscription() < $time) {
                $sortie->setEtat($etatCloture);
            }
        }
        foreach ($sortiesCreees as $sortie) {
            if ($sortie->getDateLimiteInscription() < $time) {
                $sortie->setEtat($etatCloture);
            }
        }
//
//        foreach ($sortiesCloturees as $sortie) {
//            $heureDebut=new \DateTime($sortie->getDateHeureDebut());
//
//          $dateFin=date_add( $heureDebut,date_interval_create_from_date_string("30 minutes"));
//
//            if ($sortie->getDateHeureDebut()<$time && $dateFin>$time
//            ) {
//                $sortie->setEtat($etatEnCours);
//            }
//        }




        $entityManager->persist($sortie);
        $entityManager->flush();

    }


}