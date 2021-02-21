<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Author;
use App\Entity\Book;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/generate/books", name="generate_books", methods={"GET"})
     */
    public function generateBooks(): Response
    {
        $url = "https://randomuser.me/api";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        $authors = $this->getDoctrine()
            ->getRepository(Author::class)
            ->findAll();
        $entityManager = $this->getDoctrine()->getManager();
        for ($i = 0; $i < 10; $i++) {
            $json = json_decode(curl_exec($curl), true);
            $book = new Book();
            $book->setName($json['results'][0]['name']['first']);
            $book->setDescription($json['results'][0]['login']['sha256']);
            $book->setYear(intval(substr($json['results'][0]['registered']['date'], 0, 4)));
            $book->setImage($json['results'][0]['picture']['medium']);
            $authorsCount = array_rand($authors) % 5;
            for ($j = 0; $j < $authorsCount; $j++) {
                $book->addAuthor($authors[array_rand($authors)]);
            }
            $entityManager->persist($book);
        }
        curl_close($curl);
        $entityManager->flush();

        return new Response("Books generated successfully");
    }

    /**
     * @Route("/generate/authors", name="generate_authors", methods={"GET"})
     */
    public function generateAuthors(): Response
    {
        $url = "https://randomuser.me/api";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        $entityManager = $this->getDoctrine()->getManager();
        for ($i = 0; $i < 10; $i++) {
            $json = json_decode(curl_exec($curl), true);
            $author = new Author();
            $author->setName($json['results'][0]['name']['first'].' '.$json['results'][0]['name']['last']);
            $entityManager->persist($author);
        }
        curl_close($curl);
        $entityManager->flush();

        return new Response("Authors generated successfully");
    }
}
