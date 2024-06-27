<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



#[Route('/category', name:'app_categorie_')]
class CategorieController extends AbstractController
{


    #[Route('/{slug}', name: 'list')]
    public function list(Categorie $categorie): Response
    {
        $products = $categorie->getProducts();
        return $this->render('categorie/list.html.twig', [
            'categorie'=>$categorie, 'products'=>$products
        ]);
    }
}
