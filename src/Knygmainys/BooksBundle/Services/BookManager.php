<?php

namespace Knygmainys\BooksBundle\Services;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\BooksBundle\Entity\HaveBook;
use Knygmainys\BooksBundle\Entity\WantBook;
use Knygmainys\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class BookManager
{
    private $em;
    private $bookRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->bookRepository = $this->em->getRepository('KnygmainysBooksBundle:Book');
    }

    public function findBookByTitle($title)
    {
        $books = $this->bookRepository->findBookByTitle($title);

        return $books;
    }

    public function findAuthor($authors)
    {
        $qb = $this->em->createQueryBuilder();
        $results = $qb->select('a')->from('Knygmainys\BooksBundle\Entity\Author', 'a')
            ->where( $qb->expr()->like('a.firstName', $qb->expr()->literal('%' . $authors . '%')) )
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        array_unshift($results, array(
            'id' => '0',
            'firstName' => 'Pridėti autorių',
            'lastName' => $authors,
        ));

        return $results;
    }

    public function findReleaseByISBN($isbn, $book)
    {
        $qb = $this->em->createQueryBuilder();
        $results = $qb->select('r')->from('Knygmainys\BooksBundle\Entity\Release', 'r')
            ->where( $qb->expr()->like('r.isbn', $qb->expr()->literal('%' . $isbn . '%')) )
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $results;
    }

    public function addWantedBook($user, $bookId)
    {
        //check if such book exists
        $book = $this->em->getRepository('KnygmainysBooksBundle:Book')->findOne($bookId);
        if (!$book) {
            return false;
        }

        //check if user already added this book
        $wantedBook = $this->em->getRepository('KnygmainysBooksBundle:WantBook')
            ->findOneBy(array(
                'user' => $user->getId(),
                'book' => $bookId
            ));

        if ($wantedBook) {
            return false;
        }

        $wantedBook = new WantBook();
        $wantedBook->setUser($user);
        $wantedBook->setBook($book);
        $wantedBook->setStatus('owned');
        $wantedBook->setUpdated();
        $wantedBook->setPoints(0);

        return true;
    }

    public function addOwnedBook($user, $bookId)
    {
        //check if such book exists
        $book = $this->em->getRepository('KnygmainysBooksBundle:Book')->findOne($bookId);
        if (!$book) {
            return false;
        }

        //check if user already added this book
        $ownedBook = $this->em->getRepository('KnygmainysBooksBundle:HaveBook')
            ->findOneBy(array(
                'user' => $user->getId(),
                'book' => $bookId
            ));

        if ($ownedBook) {
            return false;
        }

        $haveBook = new HaveBook();
        $haveBook->setUser($user);
        $haveBook->setBook($book);
        $haveBook->setStatus('owned');
        $haveBook->setUpdated();

        return true;

    }

    /**
     * create json response
     *
     * @var string $message message to define action state
     * @var string $status status variable to tell js functions about state
     * @var integer $statusCode response status code
     *
     * @return object
     */
    public function createJSonResponse($message, $status, $statusCode)
    {
        $responseBody = json_encode(array('message' => $message, 'status' => $status));
        $response = new Response($responseBody, $statusCode, array(
            'Content-Type' => 'application/json'
        ));

        return $response;
    }
}