<?php

namespace Knygmainys\BooksBundle\Service;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\BooksBundle\Entity\HaveBook;
use Knygmainys\BooksBundle\Entity\WantBook;
use Knygmainys\UserBundle\Entity\User;
use Knygmainys\UserBundle\Service\NotificationService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class BookManager
{
    private $em;
    private $bookRepository;
    private $notifications;
    private $router;

    public function __construct(EntityManager $entityManager, NotificationService $notifications, Router $router)
    {
        $this->em = $entityManager;
        $this->notifications = $notifications;
        $this->router = $router;
        $this->bookRepository = $this->em->getRepository('KnygmainysBooksBundle:Book');
    }

    /**
     * create book offer for user which have book in his wanted list
     * @param User $targetUser
     * @param User $user
     * @param integer $bookId
     * @param integer $releaseId
     * @return string
     */
    public function offerBook($targetUser, $user, $bookId, $releaseId = 0)
    {
        $searchFilters = array(
            'book' => $bookId,
            'user' => $user->getId(),
            'receiver' => null
        );

        if ($releaseId != null) {
            $searchFilters['release'] = $releaseId;
        }

        $haveBook = $this->em->getRepository('KnygmainysBooksBundle:HaveBook')->findOneBy($searchFilters);

        //check if user have book to offer
        if ($haveBook == null) {
            return 'Jūs neturite tokios knygos ar knygos leidimo arba ji jau yra paskirta kitam vartotojui!';
        }

        //modify filter to check if user wants this book (do not matter if release is different)
        $searchFilters['user'] = $targetUser->getId();
        unset($searchFilters['release']);
        unset($searchFilters['receiver']);
        $wantBook = $this->em->getRepository('KnygmainysBooksBundle:WantBook')->findOneBy($searchFilters);

        //check if user have book to offer
        if ($wantBook == null) {
            return 'Vartotojo norimų knygų sąraše tokios knygos nėra!';
        }

        $message = 'Sveiki, vartotojas '.$user->getFirstName().' nori pasidalinti su jumis
        Jūsų norimų knygų sąraše esančia knygą - '.$haveBook->getBook()->getTitle().'.
        Norėdami detalesnės informacijos spauskite "Peržiūrėti" mygtuką po šia žinute.';

        $url = $this->router->generate('knygmainys_books_offer', array('id' => $haveBook->getId()), true);
        $this->notifications->createNotification('Norimos knygos pasiūlymas!', $message, $url, array( array('id' => $targetUser->getId())));

        $haveBook->setStatus('pending_offer');
        $haveBook->setReceiver($targetUser);
        $this->em->persist($haveBook);
        $this->em->flush();

        return true;
    }

    /**
     * @param User $targetUser
     * @param User $user
     * @param integer $bookId
     * @param integer $releaseId
     * @return string
     */
    public function askForBook($targetUser, $user, $bookId, $releaseId)
    {
        $searchFilters = array(
            'book' => $bookId,
            'user' => $targetUser->getId(),
            'status' => 'owned',
            'receiver' => null
        );

        if ($releaseId != null) {
            $searchFilters['release'] = $releaseId;
        }

        $haveBook = $this->em->getRepository('KnygmainysBooksBundle:HaveBook')->findOneBy($searchFilters);

        //check if user have book to offer
        if ($haveBook == null) {
            return 'Vartotojo turimų knygų sąraše tokios knygos nėra arba ji rezervuota!';
        }

        $findWanted = array(
            'book' => $bookId,
            'user' => $user,
        );

        if ($releaseId != null) {
            $findWanted['release'] = $releaseId;
        }

        $wantedBook = $this->em->getRepository('KnygmainysBooksBundle:WantBook')->findOneBy($findWanted);

        if ($wantedBook == null ) {
            $book = $this->em->getRepository('KnygmainysBooksBundle:Book')->findOneBy($bookId);

            $wantedBook = new WantBook();
            $wantedBook->setUser($user);
            $wantedBook->setBook($book);
            $wantedBook->setStatus('wanted');
            $wantedBook->setUpdated();
            if ($releaseId != null) {
                $release = $this->em->getRepository('KnygmainysBooksBundle:Release')->findOneBy($releaseId);
                $wantedBook->setRelease($release);
            }
            $this->em->persist($wantedBook);
        }

        $message = 'Sveiki, vartotojas '.$user->getFirstName().' nori paprašyti
        Jūsų turimų knygų sąraše esančios knygos - '.$haveBook->getBook()->getTitle().'.
        Norėdami detalesnės informacijos spauskite "Peržiūrėti" mygtuką po šia žinute.';

        $url = $this->router->generate('knygmainys_books_request', array('id' => $haveBook->getId()), true);
        $this->notifications->createNotification('Turimos knygos prašymas!', $message, $url, array( array('id' => $targetUser->getId())));

        $user->reservePoints(1);
        $haveBook->setReceiver($user);
        $haveBook->setStatus('pending_request');
        $this->em->persist($haveBook);
        $this->em->persist($user);
        $this->em->flush();

        return true;
    }

    /**
     * get user wanted books
     * @param $userId
     * @return mixed
     */
    public function getWantedList($userId)
    {
        $books = $this->bookRepository->getWantedBooks($userId);

        return $books;
    }

    /**
     * get user owned books
     * @param $userId
     * @return mixed
     */
    public function getOwnedList($userId)
    {
        $books = $this->bookRepository->getOwnedBooks($userId);

        return $books;
    }

    /**
     * Accept book offer from user
     * @param HaveBook $bookOffer
     * @return string
     */
    public function acceptBookOffer(HaveBook $bookOffer)
    {
        try {
            $message = 'Sveiki, vartotojas '.$bookOffer->getReceiver()->getFirstName().' priėmė Jūsų pasiūlymą knygai - '.$bookOffer->getBook()->getTitle().'.
            Informaciją reikalingą knygos persiuntimui rasite vartotojo profilyje, kurį galite peržiūrėti paspaudę mygtuką po šia žinute.';
            $url = $this->router->generate('knygmainys_user_profile', array('id' => $bookOffer->getReceiver()->getId()), true);;

            $bookOffer->setStatus('closed');
            $wantedBook = $this->em->getRepository('KnygmainysBooksBundle:WantBook')
                ->findOneBy(
                    array(
                        'user' => $bookOffer->getReceiver()->getId(),
                        'book' => $bookOffer->getBook()->getId(),
                        'release' => $bookOffer->getRelease()->getId()
                    )
                );

            if ($wantedBook == null) {
                $wantedBook = $this->em->getRepository('KnygmainysBooksBundle:WantBook')
                    ->findOneBy(
                        array(
                            'user' => $bookOffer->getReceiver()->getId(),
                            'book' => $bookOffer->getBook()->getId(),
                        )
                    );
            }
            $contributor = $bookOffer->getUser();
            $receiver = $bookOffer->getReceiver();

            $contributor->addEarnedPoints(1);
            $receiver->removePoints(1);

            $wantedBook->setStatus('closed');
            $wantedBook->setContributor($contributor);

            $this->notifications->createNotification('Knygos pasiūlymas priimtas!', $message, $url, array( array('id' => $bookOffer->getUser()->getId())));

            $this->em->persist($contributor);
            $this->em->persist($receiver);
            $this->em->persist($wantedBook);
            $this->em->persist($bookOffer);
            $this->em->flush();

            return true;
        } catch (Exception $e) {
            return 'Nepavyko išsaugoti pakeitimų.';
        }
    }

    /**
     * Reject book offer from user
     * @param HaveBook $bookOffer
     * @return string
     */
    public function rejectBookOffer(HaveBook $bookOffer)
    {
        try {
            $message = 'Sveiki, vartotojas '.$bookOffer->getReceiver()->getFirstName().' atmetė Jūsų pasiūlymą knygai - '.$bookOffer->getBook()->getTitle().'.
            Knyga gražinta į turimų knygų sąraša.';

            $bookOffer->setReceiver(null);
            $bookOffer->setStatus('owned');

            $this->notifications->createNotification('Knygos pasiūlymas atmestas!', $message, '', array( array('id' => $bookOffer->getUser()->getId())));

            $this->em->persist($bookOffer);
            $this->em->flush();

            return true;
        } catch (Exception $e) {
            return 'Nepavyko išsaugoti pakeitimų.';
        }
    }

    /**
     * @param HaveBook $bookRequest
     * @return string
     */
    public function acceptBookRequest(HaveBook $bookRequest)
    {
        try {
            $message = 'Sveiki, vartotojas '.$bookRequest->getUser()->getFirstName().' priemė Jūsų prašymą knygai - '.$bookRequest->getBook()->getTitle().'.
            Knyga būs išsiūsta Jūsų profilyje nurodytu adresu.';

            $this->notifications->createNotification('Knygos prašymas priimtas!', $message, '', array( array('id' => $bookRequest->getReceiver()->getId())));

            $findWanted = array(
                'book' => $bookRequest->getBook()->getId(),
                'user' => $bookRequest->getReceiver()->getId(),
            );

            if ($bookRequest->getRelease() != null) {
                $findWanted['release'] = $bookRequest->getRelease()->getId();
            }

            $receiver = $bookRequest->getReceiver();
            $contributor = $bookRequest->getUser();

            $wantedBook = $this->em->getRepository('KnygmainysBooksBundle:WantBook')->findOneBy($findWanted);

            if ($wantedBook != null ) {
                $wantedBook->setStatus('closed');
                $wantedBook->setContributor($contributor);
                $this->em->persist($wantedBook);
            }

            $receiver->removeFromReserve(1);
            $contributor->addEarnedPoints(1);
            $bookRequest->setStatus('closed');
            $this->em->persist($receiver);
            $this->em->persist($contributor);
            $this->em->persist($bookRequest);
            $this->em->flush();

            return 'Prašymas sekmingai priimtas.';
        } catch (Exception $e) {
            return 'Nepavyko išsaugoti pakeitimų.';
        }
    }

    /**
     * @param HaveBook $bookRequest
     * @return string
     */
    public function rejectBookRequest(HaveBook $bookRequest)
    {
        try {
            $message = 'Sveiki, vartotojas '.$bookRequest->getUser()->getFirstName().' atmetė Jūsų prašymą knygai - '.$bookRequest->getBook()->getTitle().'.
            Knyga gražinta į turimų knygų sąraša.';

            $receiver = $bookRequest->getReceiver();
            $receiver->removeFromReserve(1);
            $receiver->addPoints(1);

            $bookRequest->setReceiver(null);
            $bookRequest->setStatus('owned');

            $this->notifications->createNotification('Knygos pasiūlymas atmestas!', $message, '', array( array('id' => $bookRequest->getReceiver()->getId())));

            $this->em->persist($receiver);
            $this->em->persist($bookRequest);
            $this->em->flush();

            return 'Prašymas sekmingai atmestas.';
        } catch (Exception $e) {
            return 'Nepavyko išsaugoti pakeitimų.';
        }
    }

    /**
     * Find books by title
     * @param string $title
     * @return mixed
     */
    public function findBookByTitle($title)
    {
        $books = $this->bookRepository->findBookByTitle($title);

        return $books;
    }

    /**
     * @param string $authors
     * @return array
     */
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

    /**
     * @param string $isbn
     * @param $book
     * @return array
     */
    public function findReleaseByISBN($isbn, $book)
    {
        $qb = $this->em->createQueryBuilder();
        $results = $qb->select('r')->from('Knygmainys\BooksBundle\Entity\Release', 'r')
            ->where( $qb->expr()->like('r.isbn', $qb->expr()->literal('%' . $isbn . '%')) )
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $results;
    }

    /**
     * Add book to wanted list
     * @param $user
     * @param integer $bookId
     * @param integer $releaseId
     * @param integer $points
     * @return bool|string
     */
    public function addWantedBook($user, $bookId, $releaseId, $points)
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
        $wantBook->setStatus('wanted');
        $wantBook->setUpdated();
        $wantBook->setPoints($points);
        $user->reservePoints($points);
        $this->em->persist($user);
        $this->em->persist($wantBook);
        $this->em->flush();

        return true;
    }

    /**
     * Add book to owned list
     * @param $user
     * @param integer $bookId
     * @param integer $releaseId
     * @return bool|string
     */
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

        $this->checkWantedListForAddedBook($haveBook);

        return true;

    }

    public function checkWantedListForAddedBook(HaveBook $haveBook)
    {
        $qb = $this->em->createQueryBuilder();
        $wantedBooks = $qb->select('wb')->from('Knygmainys\BooksBundle\Entity\WantBook', 'wb')
            ->where('wb.user != '.$haveBook->getUser()->getId())
            ->andWhere('wb.book = '.$haveBook->getBook()->getId())
            ->andWhere('wb.status = :status')
            ->setParameter('status', 'owned')
            ->getQuery()
            ->getResult();

        foreach($wantedBooks as $wanted) {
            $message = 'Sveiki, vartotojas '.$haveBook->getUser()->getFirstName().' katik pridėjo
            Jūsų norimų knygų sąraše esančia knygą - '.$haveBook->getBook()->getTitle().' į savo turimų knygų sąrašą.
            Norėdami paprašyti šios knygos peržiūrėkite vartojo profilį paspausdami po žinute esantį mygtuką.';

            $url = $this->router->generate('knygmainys_user_profile', array('id' => $haveBook->getUser()->getId()), true);
            $this->notifications->createNotification('Vartotojas įkėlė Jūsų norimą knygą!', $message, $url, array( array('id' => $wanted->getUser()->getId())));

        }
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