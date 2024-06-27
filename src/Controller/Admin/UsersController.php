<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin/utilisateur', name: 'admin_user')]
class UsersController extends AbstractController
{

 #[Route('/', name:'index')]
 public function index(): Response
 {
    return $this->render('admin//users/index.html.twig');
 }

}