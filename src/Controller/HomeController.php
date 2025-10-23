<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\MessageGenerator;
use App\Service\HappyQuote\Happy;


final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'identifiant'=>5
        ]);
    }
     #[Route('/hello', name: 'hello')]
     public function hello(): Response
    {
        return new Response("hello 3A26") ;
    }
     #[Route('/contact/{tel}', name: 'contact')]
    public function contact($tel): Response
    {
        return $this->render('home/contact.html.twig',
        ['telephone'=>$tel]
    );
    }
     #[Route('/show', name: 'show')]
     public function show(): Response
    {
        return new Response("Bienvenue") ;
    }
     #[Route('/affiche/{name}', name: 'affiche')]
     public function affichier($name): Response
    {
         return $this->render('home/apropos.html.twig',
         ['nameUser'=>$name]
    );
    }
    #[Route('/happy', name: 'happy')]
public function home(MessageGenerator $messageGenerator): Response
{
$message = $messageGenerator->getHappyMessage();
return new Response("<h1>Citation du jour :</h1><p>$message</p>");
} 
 #[Route('/happy2', name: 'Happy')]
public function HomeHappy(Happy $Happy): Response
{
$message = $Happy->getHappyMessage();
return $this->render('auth/showAll.html.twig'
    );
} 
}
