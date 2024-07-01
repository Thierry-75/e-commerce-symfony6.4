<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/category', name:'app_categorie_')]
class CategorieController extends AbstractController
{

    #[Route('/{slug}', name: 'list')]
    public function list(Categorie $categorie, ProductRepository $productRepository,Request $request): Response
    {
        //num page on url
        $page = $request->query->getInt('page',1);
        $products = $productRepository->findProduitsPaginate($page,$categorie->getSlug(),8);
        
        return $this->render('categorie/list.html.twig', [
            'categorie'=>$categorie, 'products'=>$products
        ]);
    }
}
