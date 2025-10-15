<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Author;
use App\Form\AuthorType;

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
         return $this->render('auth/showAll.html.twig', [
        'list' => $authors,
    ]);
        
   }
     #[Route('/add', name: 'add')]
  public function add(ManagerRegistry $doctrine): Response{
$author=new Author();
$author->setEmail (email: 'foulen@esprit.tn');
$author->setUsername (username: 'foulen');
$em=$doctrine->getManager();
$em->persist(object: $author);
$em->flush();
return $this->redirectToRoute('showAll');
//return new Response(content: "Author added suceesfully");

        
   }
 #[Route('/delete/{id}', name: 'delete')]
  public function deleteAuthor($id,AuthorRepository $repo ,ManagerRegistry $doctrine):Response{
    $author=$repo->find($id);
    $em=$doctrine->getManager();
    $em->remove($author);
    $em->flush();//ajout w supprission w modification bel flush
return $this->redirectToRoute('showAll');

  }
    #[Route('/showDetails/{id}', name: 'showDetails')]
  public function show(int $id, AuthorRepository $repo): Response
{
    $author = $repo->find($id);

    if (!$author) {
        throw $this->createNotFoundException('Auteur non trouvé');
    }

    return $this->render('auth/showDetails.html.twig', [
        'author' => $author,
    ]);
        
   }
   
    #[Route('/addForm',name:'addForm')]
    public function addForm(Request $request, ManagerRegistry $doctrine){
    $author=new Author();
    $form=$this->createForm(AuthorType::class,$author);
    $form->add('Add',SubmitType::class);
 
    $form->handleRequest($request);
    if($form->isSubmitted()){
     $em=$doctrine->getManager();
     $em->persist($author);
     $em->flush();
     return $this->redirectToRoute('showAll');
    }
    return $this->render('auth/add.html.twig',['formulaire'=>$form->createView()]);
    // return $this->renderForm()
    }

    #[Route('/edit/{id}', name: 'edit_author')]
public function edit( int $id,AuthorRepository $repo, Request $request, ManagerRegistry $doctrine): Response {
 $author = $repo->find($id);
 $form = $this->createForm(AuthorType::class, $author);
 $form->add('Modifier', SubmitType::class);
 $form->handleRequest($request);
 if($form->isSubmitted()){
     $em=$doctrine->getManager();
     $em->flush();
     return $this->redirectToRoute('showAll');
    }
     return $this->render('auth/edit.html.twig', [
        'formulaire' => $form->createView(),
        'author' => $author,
    ]);
}





}
