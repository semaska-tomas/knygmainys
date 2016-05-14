<?php

namespace Knygmainys\BooksBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\BooksBundle\Entity\WantBook;
use Knygmainys\BooksBundle\Entity\HaveBook;

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
            ->where("hb.status != 'closed'")
            ->andWhere('hb.user = '.$userId)
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Get the list of user received books
     * @param integer $userId
     * @return array
     */
    public function getReceivedBooks($userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('hb, r, b')->from('Knygmainys\BooksBundle\Entity\HaveBook', 'hb')
            ->leftJoin('hb.book', 'b')
            ->leftJoin('hb.release', 'r')
            ->where("hb.status = 'closed'")
            ->andWhere('hb.receiver = '.$userId)
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
            ->where("wb.status != 'closed'")
            ->andWhere('wb.user = '.$userId)
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Get the list of user contributed books
     * @param integer $userId
     * @return array
     */
    public function getContributedBooks($userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('hb, r, b')->from('Knygmainys\BooksBundle\Entity\HaveBook', 'hb')
            ->leftJoin('hb.book', 'b')
            ->leftJoin('hb.release', 'r')
            ->where("hb.status = 'closed'")
            ->andWhere('hb.user = '.$userId)
            ->andWhere('hb.receiver != 0')
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
            ->andWhere("wb.status = 'wanted'")
            ->andWhere('wb.user !='.$userId)
            ->groupby('wb.user')
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
            ->andWhere("hb.status = 'owned'")
            ->andWhere('hb.user !='.$userId)
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