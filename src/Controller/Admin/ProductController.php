<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin/produit', name: 'admin_produit')]
class ProductController extends AbstractController
{

   #[Route('/', name: 'index')]
   public function index(): Response
   {
      return $this->render('admin/product/index.html.twig');
   }
}
