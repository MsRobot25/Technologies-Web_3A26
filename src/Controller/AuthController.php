<?php

namespace App\Controller;
use App\Service\HappyQuote\HappyQuote;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Author;
use App\Form\AuthorType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\SearchAuthorType;

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
public function listAuthorss(): Response
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
#[Route('/authors/by-email', name: 'app_author_by_email')]
public function listByEmail(AuthorRepository $repo): Response
{
    $authors = $repo->listAuthorByEmail();
    return $this->render('auth/list_by_email.html.twig', [
        'authors' => $authors,
    ]);
}

#[Route(path: '/ShowAllAuthorsDQL', name:'ShowAll Authors DQL' )]
public function ShowAllAuthorsDQL(){
$query=$this->getEntityManager ()
->createQuery(dql: 'SELECT a FROM App\Entity Author a WHERE
a. username LIKE :condition')
->setParameter(key: 'condition ', value: '%a%')
->getResult();
return $query;
}

#[Route('/authors', name: 'list_authors')]
public function listAuthors(Request $request, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(SearchAuthorType::class);
    $form->handleRequest($request);
    $queryBuilder = $entityManager->getRepository(Author::class)->createQueryBuilder('a');

    if ($form->isSubmitted()) {
        $data = $form->getData();

      if ($data['minBooks'] !== null) {
    $queryBuilder->andWhere('a.nb_books >= :minBooks')
                 ->setParameter('minBooks', $data['minBooks']);
}

if ($data['maxBooks'] !== null) {
    $queryBuilder->andWhere('a.nb_books <= :maxBooks')
                 ->setParameter('maxBooks', $data['maxBooks']);
}

    }
    $authors = $queryBuilder->getQuery()->getResult();
    return $this->render('auth/listSearch.html.twig', [
        'list' => $authors,
        'form' => $form->createView(),
    ]);
}

#[Route('/authors/delete-zero-books', name: 'delete_zero_books_authors_dql')]
public function deleteZeroBooksAuthorsDQL(EntityManagerInterface $entityManager): Response
{
    $query = $entityManager->createQuery(
        'DELETE FROM App\Entity\Author a WHERE a.nb_books = 0'
    );

    $query->execute();

    return $this->redirectToRoute('showAll');
}
#[Route('/showAll2', name: 'showAll2')]
public function showAllHappy(AuthorRepository $repo,  HappyQuote $happy): Response
{
    $authors = $repo->findAll();
    $happyMessage = $happy->getHappyMessage();

    return $this->render('auth/showAll.html.twig', [
        'list' => $authors,
        'happyMessage' => $happyMessage,
    ]);
}



}




