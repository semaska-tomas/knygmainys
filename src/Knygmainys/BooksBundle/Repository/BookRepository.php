<?php

namespace Knygmainys\BooksBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Knygmainys\BooksBundle\Entity\Book;

class BookRepository extends EntityRepository
{

    public function findBookByTitle($title)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('b')->from('Knygmainys\BooksBundle\Entity\Book', 'b')
            ->where( $qb->expr()->like('b.title', $qb->expr()->literal('%' . $title . '%')) )
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $results;
    }

    public function getBookAuthors($bookId)
    {
        $query = $this->getEntityManager()->createQuery("
            SELECT a.id, a.firstName, a.lastName
                FROM KnygmainysBooksBundle:Author a
                JOIN KnygmainysBooksBundle:BookAuthor ba
                WITH ba.author = a.id
                WHERE ba.book = :book")
            ->setParameter('book', $bookId);

        return $query->getResult();
    }

    public function getBookReleases($bookId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('ra, b')->from('Knygmainys\BooksBundle\Entity\BookRelease', 'ra')
            ->leftJoin('ra.book', 'b')
            ->where( $qb->expr()->eq('b.id', $bookId) )
            ->getQuery()
            ->getResult();

        return $results;
    }
}