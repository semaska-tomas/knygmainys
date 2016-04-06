<?php

namespace Knygmainys\BooksBundle\Services;

use Knygmainys\BooksBundle\Entity\Book;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class BookManager
{
    private $entityManager;
    private $bookRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->bookRepository = $this->entityManager->getRepository('KnygmainysBooksBundle:Book');
    }

    public function findBookByTitle($title)
    {
        $books = $this->bookRepository->findBookByTitle($title);

        return $books;
    }

    public function findAuthor($authors)
    {
        $qb = $this->entityManager->createQueryBuilder();
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