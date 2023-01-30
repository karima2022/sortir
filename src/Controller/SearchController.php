<?php

namespace App\Controller;

use App\filtre\recherche;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="app_search")
     */
    public function search(SortieRepository $sortieRepository, CampusRepository $campusRepository, Request $request): Response
    {
  $searchForm= new recherche();





        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }
}
