<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ReaderRepository;
use App\Entity\Reader;
use App\Entity\Book;
use App\Form\ReaderType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
#[Route('/reader')]
final class ReaderController extends AbstractController
{
    #[Route('/reader', name: 'app_reader')]
    public function index(): Response
    {
        return $this->render('reader/index.html.twig', [
            'controller_name' => 'ReaderController',
        ]);
    }

 #[Route('/showReaders', name: 'app_reader_list')]
    public function listReaders(ReaderRepository $readerRepository): Response
{
    $readers = $readerRepository->findAll();

    return $this->render('reader/list.html.twig', [
        'readers' => $readers,
    ]);
}

#[Route('/addReader', name: 'app_reader_add')]
    public function addReader(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reader = new Reader();
        $form = $this->createForm(ReaderType::class, $reader);
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
         foreach ($reader->getBooks() as $book) {
        $book->addReader($reader); 
    }

    $entityManager->persist($reader);
    $entityManager->flush();

    return $this->redirectToRoute('app_reader_list');
}

        return $this->render('reader/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reader_edit')]
    public function editReader(Request $request, Reader $reader, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReaderType::class, $reader);
        $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
    // Synchronize both sides
    foreach ($reader->getBooks() as $book) {
        $book->addReader($reader);
    }
    $allBooks = $entityManager->getRepository(Book::class)->findAll();
    foreach ($allBooks as $book) {
        if (!$reader->getBooks()->contains($book)) {
            $book->removeReader($reader);
        }
    }

    $entityManager->flush();
    return $this->redirectToRoute('app_reader_list');
}

        return $this->render('reader/edit.html.twig', [
            'reader' => $reader,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/showDetails/{id}', name: 'app_reader_show')]
    public function showReader(Reader $reader): Response
    {
        return $this->render('reader/show.html.twig', [
            'reader' => $reader,
            'books' => $reader->getBooks(), 
        ]);
    }

    #[Route('/{id}/books', name: 'app_reader_books')]
public function showReaderBooks(Reader $reader): Response
{
    return $this->render('reader/books.html.twig', [
        'reader' => $reader,
        'books' => $reader->getBooks(),
    ]);
}

#[Route('/{id}', name: 'app_reader_delete')]
public function deleteReader(Reader $reader, EntityManagerInterface $entityManager): Response
{
    foreach ($reader->getBooks() as $book) {
        $book->removeReader($reader);
    }
    $reader->getBooks()->clear();
    $entityManager->remove($reader);
    $entityManager->flush();

    return $this->redirectToRoute('app_reader_list');
}



}
