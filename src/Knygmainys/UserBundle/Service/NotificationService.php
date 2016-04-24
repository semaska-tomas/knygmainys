<?php

namespace Knygmainys\UserBundle\Service;

use Knygmainys\UserBundle\Entity\Notification;
use Knygmainys\UserBundle\Entity\NotificationUser;
use Knygmainys\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

class NotificationService
{
    private $entityManager;
    private $router;

    public function __construct(EntityManager $entityManager,RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * create notification
     *
     * @var string $title
     * @var string $text
     * @var string $url
     * @var array $users
     *
     */
    public function createNotification($title, $text, $url, $users)
    {
        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setText($text);
        $notification->setLink($url);
        $notification->setDate(new \DateTime('now'));
        $this->entityManager->persist($notification);

        foreach ($users as $user) {
            $user = $this->entityManager->getRepository('KnygmainysUserBundle:User')->find($user['id']);
            $notificationUser = new NotificationUser();
            $notificationUser->setUser($user);
            $notificationUser->setNotification($notification);
            $notificationUser->setSeen(false);
            $this->entityManager->persist($user);
            $this->entityManager->persist($notificationUser);
        }
        $this->entityManager->flush();
    }

    /**
     * get unread notifications count dedicated to user
     *
     * @var integer $user
     *
     * @return integer
     */
    public function getUnreadNotificationsCount($user)
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('COUNT(n.notification)')
            ->from('KnygmainysUserBundle:NotificationUser','n')
            ->where('n.user = ?1')
            ->andWhere('n.seen = false')
            ->setParameter(1, $user);
        $count = $query->getQuery()->getSingleScalarResult();

        return $count;
    }
}
