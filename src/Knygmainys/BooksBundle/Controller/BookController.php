<?php

namespace Knygmainys\BooksBundle\Controller;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\BooksBundle\Entity\Author;
use Knygmainys\BooksBundle\Entity\BookAuthor;
use Knygmainys\BooksBundle\Form\SearchType;
use Knygmainys\BooksBundle\Entity\HaveBook;
use Knygmainys\BooksBundle\Service\BookManager;
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

    /**
     * Main books page
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $paginator = $this->get('knp_paginator');
        $qb = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KnygmainysBooksBundle:Book')
            ->createQueryBuilder('book');

        $query = $qb->select('book');

        $searchForm = $this->get('form.factory')->createNamed('', new SearchType());
        $searchForm->handleRequest($request);

        //filter books if search form is submitted
        if ($searchForm->isSubmitted()) {
            $formData = $searchForm->getData();

            if (!is_null($formData['search_text'])) {
                $query = $query->where('book.title LIKE :searchText')
                    ->setParameter('searchText', '%' . $formData['search_text'] . '%');
            }

            if (!$formData['category']->isEmpty()) {
                $query = $query->andWhere('book.category IN (:category)')
                    ->setParameter('category', $formData['category']->toArray());
            }

            if ($formData['type'] != null) {
                if ($formData['type'] == 'wanted') {
                    $query = $query->innerJoin('book.wantedBook', 'wanted');
                } elseif ($formData['type'] == 'owned') {
                    $query = $query->innerJoin('book.ownedBook', 'owned');
                }
            }
        }

        $query = $query
            ->orderBy('book.created')
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
     * Create book page
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
            $book->setCreated();
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('knygmainys_books_release_create', array('id' => $book->getId()));
        }

        return $this->render('KnygmainysBooksBundle:Book:create.html.twig', [
            'form' => $form->createView(),
            'customErrors' => $customErrors
        ]);
    }

    /**
     * Show book by id
     * @param Book $book
     * @return Response
     */
    public function showAction(Book $book)
    {
        $bookRepository = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:Book');
        $authors = $bookRepository->getBookAuthors($book->getId());
        $releases = $bookRepository->getBookReleases($book->getId());
        $wantedBy = $bookRepository->getWantedBy($book->getId(), $this->getUser()->getId());
        $ownedBy = $bookRepository->getOwnedBy($book->getId(), $this->getUser()->getId());
        $rootDir = $this->get('kernel')->getRootDir();
        $rootDir = preg_replace("/app/", "", $rootDir);
        return $this->render('KnygmainysBooksBundle:Book:show.html.twig', [
            'book' => $book,
            'releases' => $releases,
            'authors' => $authors,
            'wantedBy' => $wantedBy,
            'ownedBy' => $ownedBy,
            'rootDir' => $rootDir
        ]);
    }

    /**
     * Show user wanted books
     * @param Request $request
     * @return Response
     */
    public function wantedAction(Request $request)
    {
        $bookRepository = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:Book');
        $wantedBooks = $bookRepository->getWantedBooks($this->getUser()->getId());

        return $this->render('KnygmainysBooksBundle:Book:wanted.html.twig', [
            'wantedBooks' => $wantedBooks
        ]);
    }

    /**
     * Show user received books
     * @param Request $request
     * @return Response
     */
    public function receivedAction(Request $request)
    {
        $bookRepository = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:Book');
        $receivedBooks = $bookRepository->getReceivedBooks($this->getUser()->getId());

        return $this->render('KnygmainysBooksBundle:Book:received.html.twig', [
            'receivedBooks' => $receivedBooks
        ]);
    }

    /**
     * Show user contributed books
     * @param Request $request
     * @return Response
     */
    public function contributedAction(Request $request)
    {
        $bookRepository = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:Book');
        $contributedBooks = $bookRepository->getContributedBooks($this->getUser()->getId());

        return $this->render('KnygmainysBooksBundle:Book:contributed.html.twig', [
            'contributedBooks' => $contributedBooks
        ]);
    }

    /**
     * Show user owned books
     * @param Request $request
     * @return Response
     */
    public function ownedAction(Request $request)
    {
        $bookRepository = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:Book');
        $ownedBooks = $bookRepository->getOwnedBooks($this->getUser()->getId());

        return $this->render('KnygmainysBooksBundle:Book:owned.html.twig', [
            'ownedBooks' => $ownedBooks
        ]);
    }

    /**
     * Show book by id
     * @param HaveBook $haveBook
     * @return Response
     */
    public function offerAction(HaveBook $haveBook)
    {
        if (($haveBook->getReceiver()->getId() == $this->getUser()->getId() || $haveBook->getUser()->getId() == $this->getUser()->getId()) && $haveBook->getStatus() != 'closed') {
            return $this->render('KnygmainysBooksBundle:Book:offer.html.twig', [
                'offeredBook' => $haveBook
            ]);
        }
        return $this->redirect($this->generateUrl('knygmainys_main_homepage'));
    }

    /**
     * @param Request $request
     * @return object|JsonResponse
     */
    public function offerStatusAction(Request $request)
    {
        $bookManager = $this->get('knygmainys_books.book_manager');
        if ($request->isXmlHttpRequest()) {
            $bookId = intval(strip_tags($request->request->get('id')));
            $action = strip_tags($request->request->get('action'));
            $haveBook = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:HaveBook')->find($bookId);

            if (($haveBook->getReceiver()->getId() == $this->getUser()->getId() || ($haveBook->getUser()->getId() == $this->getUser()->getId() && $action == 'reject')))
            {
                $msg = '';
                if ($action == 'accept') {
                    $msg = $bookManager->acceptBookOffer($haveBook);
                } elseif ($action == 'reject') {
                    $msg = $bookManager->rejectBookOffer($haveBook);
                }

                return $bookManager->createJSonResponse($msg, 'ok', 200);
            }
            return $bookManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);
        } else {
            return $bookManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);
        }
    }

    /**
     * Show book by id
     * @param HaveBook $haveBook
     * @return Response
     */
    public function requestAction(HaveBook $haveBook)
    {
        if (($haveBook->getUser()->getId() == $this->getUser()->getId() || $haveBook->getReceiver()->getId() == $this->getUser()->getId()) && $haveBook->getStatus() != 'closed') {
            return $this->render('KnygmainysBooksBundle:Book:request.html.twig', [
                'askedBook' => $haveBook
            ]);
        }
        return $this->redirect($this->generateUrl('knygmainys_main_homepage'));
    }

    /**
     * @param Request $request
     * @return object|JsonResponse
     */
    public function requestStatusAction(Request $request)
    {
        $bookManager = $this->get('knygmainys_books.book_manager');
        if ($request->isXmlHttpRequest()) {
            $bookId = intval(strip_tags($request->request->get('id')));
            $action = strip_tags($request->request->get('action'));
            $bookRequest = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysBooksBundle:HaveBook')->find($bookId);

            if (($bookRequest->getUser()->getId() == $this->getUser()->getId() || $bookRequest->getReceiver()->getId() == $this->getUser()->getId()) && $bookRequest->getStatus() != 'closed')
            {
                $msg = '';
                $status = 'failed';
                if ($action == 'accept' && $bookRequest->getReceiver()->getId() != $this->getUser()->getId()) {
                    $msg = $bookManager->acceptBookRequest($bookRequest);
                    if ($msg) {
                        $msg = 'Prašymas sekmingai priimtas.';
                        $status = 'ok';
                    }
                } elseif ($action == 'reject') {
                    $msg = $bookManager->rejectBookRequest($bookRequest);
                    if ($msg) {
                        $msg = 'Prašymas sekmingai atmestas.';
                    }
                }

                return $bookManager->createJSonResponse($msg, $status, 200);
            }

            return $bookManager->createJSonResponse('Jūs neturite priegos!', 'failed', 200);
        } else {
            return $bookManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);
        }
    }

    /**
     * @param Request $request
     * @return object|JsonResponse
     */
    public function addBookToUserAction(Request $request)
    {
        $bookManager = $this->get('knygmainys_books.book_manager');
        if ($request->isXmlHttpRequest()) {
            $bookId = intval(strip_tags($request->request->get('id')));
            $releaseId = intval(strip_tags($request->request->get('release')));
            $action = strip_tags($request->request->get('action'));
            $targetUserId = intval(strip_tags($request->request->get('user')));

            try {
                $targetUser = $this->get('doctrine.orm.entity_manager')->getRepository('KnygmainysUserBundle:User')->find($targetUserId);

                if ($targetUser) {
                    $user = $this->getUser();
                    $msg = false;
                    if ($action === 'offer') {
                        $msg = $bookManager->offerBook($targetUser, $user, $bookId, $releaseId);
                    } elseif ($action === 'ask') {
                        $msg = $bookManager->askForBook($targetUser, $user, $bookId, $releaseId);
                        if ($msg === true) {
                            return $bookManager->createJSonResponse('Užklausa sekmingai pridėta, laukite kol ją patvirtins gavėjas!', 'ok', 200);
                        }
                     }
                }

                return $bookManager->createJSonResponse($msg, 'failed', 200);
            } catch (Exception $e) {
                return $bookManager->createJSonResponse($e->getMessage(), 'failed', 200);
            }
        } else {
            return $bookManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);
        }
    }

    /**
     * Add book to wanted or owned lists
     * @param Request $request
     * @return object
     */
    public function addBookToListAction(Request $request)
    {
        $bookManager = $this->get('knygmainys_books.book_manager');
        if ($request->isXmlHttpRequest()) {
            $bookId = intval(strip_tags($request->request->get('id')));
            $releaseId = intval(strip_tags($request->request->get('release')));
            $action = strip_tags($request->request->get('action'));
            try {
                $user = $this->getUser();
                $msg = false;
                if ($action === 'wanted') {
                    $points = strip_tags($request->request->get('points'));
                    if ($points == null) {
                        $points = 1;
                    }
                    if ($points < $user->getCurrentPoints() && $points >= 0) {
                        $msg = $bookManager->addWantedBook($user, $bookId, $releaseId, $points);
                    } else {
                        return $bookManager->createJSonResponse('Jūs neturite tiek taškų!', 'failed', 200);
                    }
                } elseif ($action === 'owned') {
                    $msg = $bookManager->addOwnedBook($user, $bookId, $releaseId);
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

    /**
     * Search book action to return ajax response
     * @param Request $request
     * @return object|JsonResponse
     */
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

    /**
     * Search author action to ajax response
     * @param Request $request
     * @return object|JsonResponse
     */
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
