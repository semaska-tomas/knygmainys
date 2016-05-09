<?php

namespace Knygmainys\BooksBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Knygmainys\BooksBundle\Entity\Book;

class BookRepository extends EntityRepository
{

    /**
     * @param string $title
     * @return array
     */
    public function findBookByTitle($title)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('b')->from('Knygmainys\BooksBundle\Entity\Book', 'b')
            ->where( $qb->expr()->like('b.title', $qb->expr()->literal('%' . $title . '%')) )
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $results;
    }

    /**
     * Get the list of user owned books
     * @param integer $userId
     * @return array
     */
    public function getOwnedBooks($userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('hb, r, b')->from('Knygmainys\BooksBundle\Entity\HaveBook', 'hb')
            ->leftJoin('hb.book', 'b')
            ->leftJoin('hb.release', 'r')
            ->where('hb.user = '.$userId)
            ->andWhere('hb.status = :status')
            ->setParameter('status', 'owned')
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Get the list of user wanted books
     * @param integer $userId
     * @return array
     */
    public function getWantedBooks($userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('wb, r, b')->from('Knygmainys\BooksBundle\Entity\WantBook', 'wb')
            ->leftJoin('wb.book', 'b')
            ->leftJoin('wb.release', 'r')
            ->where('wb.user = '.$userId)
            ->andWhere('wb.status = :status')
            ->setParameter('status', 'wanted')
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Get users who wants specified book
     * @param $bookId
     * @param $userId
     * @return array
     */
    public function getWantedBy($bookId, $userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('wb, r, b')->from('Knygmainys\BooksBundle\Entity\WantBook', 'wb')
            ->leftJoin('wb.book', 'b')
            ->leftJoin('wb.release', 'r')
            ->where('b.id = '.$bookId)
            ->andWhere('wb.status = :status')
            ->andWhere('wb.user !='.$userId)
            ->groupby('wb.user')
            ->setParameter('status', 'wanted')
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Get users who owns specified book
     * @param $bookId
     * @param $userId
     * @return array
     */
    public function getOwnedBy($bookId, $userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('hb, r, b')->from('Knygmainys\BooksBundle\Entity\HaveBook', 'hb')
            ->leftJoin('hb.book', 'b')
            ->leftJoin('hb.release', 'r')
            ->where('b.id = '.$bookId)
            ->andWhere('hb.status = :status')
            ->andWhere('hb.user !='.$userId)
            ->setParameter('status', 'owned')
            ->groupby('hb.user')
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Get the list of book authors
     * @param string $bookId
     * @return array
     */
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

    /**
     * Get the list of book releases
     * @param integer $bookId
     * @return array
     */
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