<?php

namespace Knygmainys\BooksBundle\Controller;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\BooksBundle\Entity\Author;
use Knygmainys\BooksBundle\Entity\BookAuthor;
use Knygmainys\BooksBundle\Form\SearchType;
use Knygmainys\BooksBundle\Services\BookManager;
use Knygmainys\BooksBundle\Form\BookFormType;
use Knp\Component\Pager\Paginator;
use Symfony\Component\Form\FormFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookController extends Controller
{
    const BOOKS_PER_PAGE = 8;

    public function indexAction(Request $request)
    {
        $paginator = $this->get('knp_paginator');
        $qb = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KnygmainysBooksBundle:Book')
            ->createQueryBuilder('b');

        $query = $qb->select('b');

        $searchForm = $this->get('form.factory')->createNamed('', new SearchType());
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted()) {
            $formData = $searchForm->getData();

            if (!is_null($formData['search_text'])) {
                $query = $query->where('b.title LIKE :searchText')
                    ->setParameter('searchText', '%' . $formData['search_text'] . '%');
            }

            if (!$formData['category']->isEmpty()) {
                $query = $query->andWhere('b.category IN (:category)')
                    ->setParameter('category', $formData['category']->toArray());
            }

            if ($formData['type'] != null) {
                if ($formData['type'] == 'wanted') {
                    $query = $query->innerJoin('b.wantedBook', 'wanted');
                } elseif ($formData['type'] == 'owned') {
                    $query = $query->innerJoin('b.ownedBook', 'owned');
                }
            }
        }

        $query = $query
            ->orderBy('b.created')
            ->getQuery();

        $books = $paginator->paginate(
            $query,
            $request->query->getInt('page', $request->get('page')),
            self::BOOKS_PER_PAGE
        );

        return $this->render('KnygmainysBooksBundle:Book:index.html.twig', [
            'books' => $books,
            'form' => $searchForm->createView()
        ]);
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
            $authors = (array) json_decode($form->get('author')->getData());

            foreach($authors as $id=>$authorFullName) {
                $custom = explode("_", $id);
                if (count($custom) > 1) {
                    $lastName = '';
                    $namePieces = explode(" ", $authorFullName);
                    if ($namePieces > 0) {
                        $author = new Author();
                        $author->setFirstName($namePieces[0]);
                        unset($namePieces[0]);
                        foreach($namePieces as $piece) {
                            $lastName .= ' '.$piece;
                        }
                        $author->setLastName($lastName);
                    }
                    $em->persist($author);
                } else {
                    $author = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('KnygmainysBooksBundle:Author')->findOneBy(
                            array(
                                'id' => $id
                            )
                    );
                }
                $bookAuthor = new BookAuthor();
                $bookAuthor->setAuthor($author);
                $bookAuthor->setBook($book);
                $em->persist($bookAuthor);
            }

            $em->persist($book);
            $em->flush();

            $this->getRequest()->headers->set('Content-Type', 'application/json');
            $this->getRequest()->request->replace(array("bookId"=> $book->getId()));
            return $this->forward('KnygmainysBooksBundle:Release:create');
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
        $releases = $bookRepository->getBookReleases($book->getId());
        $rootDir = $this->get('kernel')->getRootDir();
        $rootDir = preg_replace("/app/", "", $rootDir);
        return $this->render('KnygmainysBooksBundle:Book:show.html.twig', [
            'book' => $book,
            'releases' => $releases,
            'authors' => $authors,
            'rootDir' => $rootDir
        ]);
    }

    public function addBookToListAction(Request $request)
    {
        $bookManager = $this->get('knygmainys_books.book_manager');
        if ($request->isXmlHttpRequest()) {
            $bookId = intval(strip_tags($request->request->get('id')));
            $release = intval(strip_tags($request->request->get('release')));
            $action = strip_tags($request->request->get('action'));

            try {
                $user = $this->getUser();
                $msg = false;
                if ($action === 'wanted') {
                    $msg = $bookManager->addWantedBook($user, $bookId, $release);
                } elseif ($action === 'owned') {
                    $msg = $bookManager->addOwnedBook($user, $bookId, $release);
                }

                if ($msg === true) {
                    return $bookManager->createJSonResponse('Knyga sekmingai pridėta!', 'ok', 200);
                }

                return $bookManager->createJSonResponse($msg, 'failed', 200);
            } catch (Exception $e) {
                return $bookManager->createJSonResponse($e->getMessage(), 'failed', 200);
            }
        } else {
            return $bookManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);
        }
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
