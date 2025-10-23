<?php

namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\SearchBookType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Form\BookType;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
#[Route('/booklist', name: 'app_book_list')]
public function listBooks(BookRepository $bookRepository): Response
{
    $publishedBooks = $bookRepository->findBy(['enabled' => true]);
    $publishedCount = $bookRepository->count(['enabled' => true]);
    $unpublishedCount = $bookRepository->count(['enabled' => false]);
    $message = empty($publishedBooks) ? 'No Books found' : null;

    return $this->render('book/list.html.twig', [
        'books' => $publishedBooks,
        'published_count' => $publishedCount,
        'unpublished_count' => $unpublishedCount,
        'message' => $message, 
    ]);
}


        #[Route('/book/add', name: 'app_book_add')]
    public function addBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setEnabled(true);
            $author = $book->getAuthor();
            if ($author) {
                $author->setNbBooks($author->getNbBooks() + 1);
            }
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('app_book_list'); 
        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
 

#[Route('/{id}/edit', name: 'app_book_edit')]
public function updateBook(Request $request, Book $book, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        return $this->redirectToRoute('app_book_list');
    }

    return $this->render('book/edit.html.twig', [
        'book' => $book,
        'form' => $form->createView(),
    ]);
}
#[Route('/book/delete/{id}', name: 'app_book_delete')]
public function deleteBook($id, BookRepository $bookRepository, ManagerRegistry $doctrine): Response
{    $book = $bookRepository->find($id);

    if (!$book) {
        return $this->redirectToRoute('app_book_list');
    }

    $author = $book->getAuthor();
    if ($author) {
        $author->setNbBooks($author->getNbBooks() - 1);
    }
    $em = $doctrine->getManager();
    $em->remove($book);
    $em->flush(); 
    return $this->redirectToRoute('app_book_list'); 
}
#[Route('/showBookDetails/{id}', name: 'app_book_show')]
public function showBook(int $id, BookRepository $bookRepository): Response
{
    $book = $bookRepository->find($id);

    return $this->render('book/show.html.twig', [
        'book' => $book,
    ]);
}

#[Route('/books/search', name: 'app_book_search')]
public function search(Request $request, BookRepository $repo): Response
{
    $form = $this->createForm(SearchBookType::class);
    $form->handleRequest($request);

    $book = null;
    $submitted = false;

    if ($form->isSubmitted() && $form->isValid()) {
        $submitted = true;
        $data = $form->getData();
        $book = $repo->searchBookById($data['id']);
    }

    return $this->render('book/search.html.twig', [
        'form' => $form->createView(),
        'book' => $book,
        'submitted' => $submitted, 
    ]);
}

#[Route(path: '/RomanceBooksCount', name: 'ShowRomanceBooksCountDQL')]
public function ShowRomanceBooksCountDQL(EntityManagerInterface $entityManager): Response
{
    $query = $entityManager->createQuery(
        'SELECT COUNT(b.id) FROM App\Entity\Book b WHERE b.category = :category'
    )
    ->setParameter('category', 'Romance');
    $count = $query->getSingleScalarResult();

    return new Response('<h2>Nbr livre de catégorie Romance :</h1> ' . $count);
}




#[Route(path: '/BooksBetweenDates', name: 'ShowBooksBetweenDatesDQL')]
public function ShowBooksBetweenDatesDQL(EntityManagerInterface $entityManager): Response
{
    $query = $entityManager->createQuery(
        'SELECT b 
         FROM App\Entity\Book b 
         WHERE b.publicationDate BETWEEN :startDate AND :endDate'
    )
    ->setParameter('startDate', new \DateTime('2014-01-01'))
    ->setParameter('endDate', new \DateTime('2018-12-31'));

    $books = $query->getResult();

    $message = empty($books) ? 'Aucun livre trouvé entre ces dates.' : null;

    return $this->render('book/list.html.twig', [
        'books' => $books,
        'message' => $message,
        'published_count' => null,
        'unpublished_count' => null,
    ]);
}





}
