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

    public function addWantedBook($user, $bookId, $releaseId)
    {
        //check if such book and release exists
        $book = $this->em->getRepository('KnygmainysBooksBundle:Book')->find($bookId);
        if (!$book) {
            return 'Tokia knyga neegzistuoja!';
        }

        if ($releaseId != 0) {
            $release = $this->em->getRepository('KnygmainysBooksBundle:BookRelease')
                    ->findOneBy(array(
                            'book' => $bookId,
                            'release' => $releaseId
                        )
                    );

            if (!$release) {
                return 'Toks knygos leidimas neegzistuoja!';
            }
        }

        //check if user already added this book
        $wantedBook = $this->em->getRepository('KnygmainysBooksBundle:WantBook')
            ->findOneBy(array(
                'user' => $user->getId(),
                'book' => $bookId,
                'release' => $releaseId
            ));

        if ($wantedBook) {
            return 'Tokia knyga jau yra Jūsų norimų knygų sąraše.';
        }

        $wantBook = new WantBook();

        if ($releaseId != 0) {
            $release = $this->em->getRepository('KnygmainysBooksBundle:Release')->find($releaseId);
            $wantBook->setRelease($release);
        }

        $wantBook->setUser($user);
        $wantBook->setBook($book);
        $wantBook->setStatus('owned');
        $wantBook->setUpdated();
        $wantBook->setPoints(0);
        $this->em->persist($wantBook);
        $this->em->flush();

        return true;
    }

    public function addOwnedBook($user, $bookId, $releaseId)
    {
        //check if such book and release exists
        $book = $this->em->getRepository('KnygmainysBooksBundle:Book')->find($bookId);
        if (!$book) {
            return 'Tokia knyga neegzistuoja!';
        }

        if ($releaseId != 0) {
            $release = $this->em->getRepository('KnygmainysBooksBundle:BookRelease')
                ->findOneBy(array(
                        'book' => $bookId,
                        'release' => $releaseId
                    )
                );

            if (!$release) {
                return 'Toks knygos leidimas neegzistuoja!';
            }
        }

        //check if user already added this book
        $ownedBook = $this->em->getRepository('KnygmainysBooksBundle:HaveBook')
            ->findOneBy(array(
                'user' => $user->getId(),
                'book' => $bookId,
                'release' => $releaseId
            ));

        if ($ownedBook) {
            return false;
        }

        $haveBook = new HaveBook();

        if ($releaseId != 0) {
            $release = $this->em->getRepository('KnygmainysBooksBundle:Release')->find($releaseId);
            $haveBook->setRelease($release);
        }

        $haveBook->setUser($user);
        $haveBook->setBook($book);
        $haveBook->setStatus('owned');
        $haveBook->setUpdated();
        $this->em->persist($haveBook);
        $this->em->flush();

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