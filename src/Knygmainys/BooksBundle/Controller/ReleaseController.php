<?php

namespace Knygmainys\BooksBundle\Controller;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\BooksBundle\Entity\BookRelease;
use Knygmainys\BooksBundle\Entity\Release;
use Knygmainys\BooksBundle\Services\BookManager;
use Knygmainys\BooksBundle\Form\ReleaseFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReleaseController extends Controller
{

    /**
     * @Template()
     */
    public function uploadAction(Request $request)
    {
        $release = new Release();
        $form = $this->createFormBuilder($release)
            ->add('name')
            ->add('file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($release);
            $em->flush();

            return $this->redirectToRoute('knygos');
        }

        return array('form' => $form->createView());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $customErrors = [];
        $release = new Release();

        $bookId = intval($request->query->get('id'));
        $book = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:Book')->find($bookId);
        $form = $this->createForm(new ReleaseFormType(), $release);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $bookRelease = new BookRelease();
            $bookRelease->setBook($book);
            $bookRelease->setRelease($release);
            $em->persist($release);
            $em->persist($bookRelease);
            $em->flush();

            return $this->redirectToRoute('knygmainys_books_show', ['id' => $book->getId()]);
        }

        return $this->render('KnygmainysBooksBundle:Release:create.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
            'customErrors' => $customErrors
        ]);
    }

    public function showAction(Book $book)
    {
        $bookRepository = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:Book');
        $authors = $bookRepository->getBookAuthors($book->getId());
        $releases = $bookRepository->getBookReleases($book->getId());

        return $this->render('KnygmainysBooksBundle:Book:show.html.twig', [
            'book' => $book,
            'releases' => $releases,
            'authors' => $authors
        ]);
    }

    public function searchISBNAction($book, Request $request)
    {
        $book = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:Book')->find($book);

        $bookManager = $this->get('knygmainys_books.book_manager');
        if ($request->isXmlHttpRequest()) {
            $string = strip_tags($request->getContent(false));
            $books = $bookManager->findReleaseByISBN($string, $book->getId());
            return new JsonResponse($books);
        } else {
            return $bookManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);
        }
    }
}
