<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\BookType;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index", methods={"GET", "HEAD"})
     */
    public function index(): Response
    {
        $books = $this->getDoctrine()
            ->getRepository(Book::class)
            ->findAll();
        return $this->render('book/index.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/native", name="book_native_query", methods={"GET", "HEAD"})
     */
    public function nativeQuery(): Response
    {
        $books = $this->getDoctrine()
            ->getRepository(Book::class)
            ->findByNativeSQL();
        return $this->render('book/native_query.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/doctrine", name="book_doctrine_query", methods={"GET", "HEAD"})
     */
    public function doctrineQuery(): Response
    {
        $books = $this->getDoctrine()
            ->getRepository(Book::class)
            ->findByDoctrineORM();
        return $this->render('book/doctrine_query.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/{id}", name="book_show", methods={"GET", "HEAD"}, requirements={"id"="\d+"})
     */
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book 
        ]);
    }

    /**
     * @Route("/", name="book_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('book_index');
        }
        return $this->render('book/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('book_index');
        }
        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Book $book): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($book);
        $entityManager->flush();
        return $this->redirectToRoute('book_index');
    }
}
