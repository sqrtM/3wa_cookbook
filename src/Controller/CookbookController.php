<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CookbookController extends AbstractController
{
    #[Route('/cookbook', name: 'app_cookbook')]
    public function index(): Response
    {
        return $this->render('cookbook/index.html.twig', [
            'controller_name' => 'CookbookController',
        ]);
    }
}
