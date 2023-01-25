<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\CreerSortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreerSortieController extends AbstractController
{
    /**
     * @Route("/creerSortie", name="creerSortie")
     */
    public function creerSortie(Request $request,EntityManagerInterface $entityManager,
                                CampusRepository $campusRepository,EtatRepository $etatRepository
    ): Response
    {
        $sortie = new Sortie();
        $user=$this->getUser();

        //$campusUser=$user->getCampus();
        $campusUser =  $campusRepository ->find($user->getCampus());
        $sortie->setCampus($campusUser);
        //dd($sortie->getCampus());
        $sortieForm = $this->createForm(CreerSortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            if(isset($_POST["enregistrer"]))
            {
               $sortie->setEtat($etatRepository->find(1));
            }
            else {$sortie->setEtat($etatRepository->find(2));}

            $entityManager ->persist($sortie);
            $entityManager ->flush();

            //todo
//             $this ->addFlash('success','Sortie créée !');
//             return  $this->redirectToRoute(' ');
        }
        return $this->render('creerSortie/creerSortie.html.twig', [
            'sortieForm' => $sortieForm ->createView(),
        ]);
    }
    /**
     * @Route("/creerSortie/list",name="afficherSortie")
     */
    public function listeSorties(SortieRepository $sortieRepository): Response
    {
       $sorties = $sortieRepository->findAll();

        return $this->render('creerSortie/listeSorties.html.twig'
            ,["sorties"=>$sorties]
            );
    }








//    /**
//     * @Route("/creerSortie/list",name="afficherDetails")
//     */
//    public function details(int $id,SortieRepository $sortieRepository): Response
//    {
//        $sortie=$sortieRepository->find($id);
//        return $this->render('creerSortie/listeSorties.html.twig',["sortie"=>$sortie]);
//    }
}
