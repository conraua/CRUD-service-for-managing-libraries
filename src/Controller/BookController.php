<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\BookType;
use App\Form\Type\FilterType;
use App\Entity\Author;
use Exception;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        $bookRepository = $this->getDoctrine()->getRepository(Book::class);
        $form = $this->createForm(FilterType::class);
        $books = $bookRepository->findAll();
        if ($request->query->get('filter')) {
            $queryParameters = array_filter($request->query->get('filter'));
            unset($queryParameters['_token']);
            if (!empty($queryParameters)) {
                $books = $bookRepository->filterBooksByParameters($queryParameters);
            }
        }
        return $this->render('book/index.html.twig', [
            'books' => $books,
            'form' => $form->createView()
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
    public function show(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('book_show');
        }
        return $this->render('book/show.html.twig', [
            'book' => $book,
            'form' => $form->createView() 
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
     * @Route("/{id}/inline", name="book_edit_inline", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function editInline(Request $request, Book $book): Response
    {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
        if ($request->request->get('name') !== null) {
            if ($request->request->get('name') === "") {
                throw new Exception("Name can't be empty");
            } else {
                $book->setName($request->request->get('name'));
            }
        }
        if ($request->request->get('description') !== null) {
            if ($request->request->get('description') === "") {
                throw new Exception("Description can't be empty");
            } else {
                $book->setDescription($request->request->get('description'));
            }
        }
        if ($request->request->get('year') !== null) {
            if ($request->request->get('year') === "") {
                throw new Exception("Year can't be empty");
            } else {
                $book->setYear($request->request->get('year'));
            }
        }
        if ($request->request->get('image') !== $book->getImage()) {
            $book->setImage($request->request->get('image'));
        }
        if (count($request->request->get('authors')) > 0) {
            foreach($book->getAuthors() as $author) {
                $book->removeAuthor($author);
            }
            foreach($request->request->get('authors') as $author_id) {
                $author = $this->getDoctrine()->getRepository(Author::class)->find($author_id);
                $book->addAuthor($author);
            }
        } else {
            throw new Exception("At least one author required");
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return new Response("Updated successfully!");
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
