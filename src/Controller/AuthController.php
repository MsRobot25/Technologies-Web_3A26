<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AuthorRepository;
final class AuthController extends AbstractController
{
    #[Route('/auth', name: 'app_auth')]
    public function index(): Response
    {
        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }
     #[Route('/auth/{name}', name: 'showAuthor')]
     public function showAuthor($name): Response
    {
         return $this->render('auth/show.html.twig',
         ['nameUser'=>$name]
    );}

    #[Route('/authors', name: 'list_authors')]
public function listAuthors(): Response
{
    $authors = [
        ['id' => 1, 'picture' => '/assets/images/default.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
        ['id' => 2, 'picture' => '/assets/images/testingshi.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
        ['id' => 3, 'picture' => '/assets/images/testlogo.png', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
    ];

    return $this->render('auth/list.html.twig',
     ['authors' => $authors,]);
}
#[Route('/authors/details/{id}', name: 'author_details')]
public function authorDetails(int $id): Response
{
    $authors = [
        1 => ['id' => 1, 'picture' => '/assets/images/default.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
        2 => ['id' => 2, 'picture' => '/assets/images/testingshi.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
        3 => ['id' => 3, 'picture' => '/assets/images/testlogo.png', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
    ];

    $author = $authors[$id] ?? null;

    if (!$author) {
        throw $this->createNotFoundException("non trouvé");
    }

    return $this->render('auth/showAuthor.html.twig', [
        'author' => $author,
    ]);
}
  #[Route('/showAll', name: 'showAll')]
  public function showAll(AuthorRepository $repo): Response
    {
        $authors=$repo->findAll();
         return $this->render('auth/showAll.html.twig', 
         parameters:['list'=>$authors]);

        
   }



}
