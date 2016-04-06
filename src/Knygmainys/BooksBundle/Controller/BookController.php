<?php

namespace Knygmainys\BooksBundle\Controller;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\BooksBundle\Entity\Release;
use Knygmainys\BooksBundle\Services\BookManager;
use Knygmainys\BooksBundle\Form\BookFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookController extends Controller
{
    public function indexAction()
    {
        echo 'asd ';
        return $this->render('KnygmainysBooksBundle:Default:index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $customErrors = [];
        $book = new Book();

        $form = $this->createForm(new BookFormType(), $book);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {

            $authors = $form->get('author')->getData();
            print_r($authors);
            print_r($request->request->get('author'));
            die('End.');

            $em->persist($book);
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('knygmainys_books_show', ['id' => $book->getId()]);
        }

        return $this->render('KnygmainysBooksBundle:Book:create.html.twig', [
            'form' => $form->createView(),
            'customErrors' => $customErrors
        ]);
    }

    public function showAction(Book $book)
    {
        $bookRepository = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:Book');
        $authors = $bookRepository->getBookAuthors($book->getId());

        return $this->render('KnygmainysBooksBundle:Book:show.html.twig', [
            'book' => $book,
            'authors' => $authors
        ]);
    }

    public function searchBookByTitleAction(Request $request)
    {
        $bookManager = $this->get('knygmainys_books.book_manager');
        if ($request->isXmlHttpRequest()) {
            $string = strip_tags($request->getContent(false));
            $books = $bookManager->findBookByTitle($string);
            return new JsonResponse($books);
        } else {
            return $bookManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);
        }
    }

    public function searchAuthorAction(Request $request)
    {
        $bookManager = $this->get('knygmainys_books.book_manager');
        if ($request->isXmlHttpRequest()) {
            $string = strip_tags($request->getContent(false));
            $authors = $bookManager->findAuthor($string);
            return new JsonResponse($authors);
        } else {
            return $bookManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);
        }
    }
}
