<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        
        return $this->render('main/index.html.twig', [
            'categories'=>$categorieRepository->findBy([],['categoryOrder'=>'ASC'])
        ]);
    }
}
