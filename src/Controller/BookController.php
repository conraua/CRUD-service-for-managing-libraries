<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book_index")
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
}
