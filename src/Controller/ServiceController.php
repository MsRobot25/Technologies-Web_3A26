<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function index(): Response
    {
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
     #[Route('/ShowService/{name}', name: 'showService')]
     public function showService($name): Response
    {
         return $this->render('service/showService.html.twig',
         ['nameUser'=>$name]
    );
    }   
     #[Route('/indexHome', name: 'goToIndex')]
     public function goToIndex(): Response
    {
         return $this->render('home2/index.html.twig',
      
    );
    }
}
