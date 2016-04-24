<?php

namespace Knygmainys\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{

    const NOTIFICATIONS_PER_PAGE = 12;

    /**
     * Show notifications
     *
     * @param Request $request
     * @param int $page
     * @return Response
     */
    public function showAction(Request $request, $page)
    {
        $em = $this->getDoctrine()->getManager()->createQueryBuilder();

        $query = $em->select('n, u.seen')
                    ->from('KnygmainysUserBundle:Notification', 'n')
                    ->leftJoin('Knygmainys\UserBundle\Entity\NotificationUser',
                        'u',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'n.id = u.notification')
                    ->where('u.user = :user')
                    ->setParameter('user', $this->getUser());

        $notifications = $this->get('knp_paginator')->paginate(
            $query,
            $request->query->getInt('page', $page),
            self::NOTIFICATIONS_PER_PAGE
        );

        return $this->render('KnygmainysUserBundle:Notification:show.html.twig', [
          'notifications' => $notifications,
      ]);
    }

    public function deleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = intval(strip_tags($request->request->get('id')));

            $em = $this->getDoctrine()->getManager();

            $repository = $em->getRepository('KnygmainysUserBundle:NotificationUser');
            $notifiedUser = $repository->findOneBy(array('notification'=> $id, 'user'=> $this->getUser()->getId() ));

            if ($notifiedUser != null) {
                $query = $em->createQuery('
                                   SELECT COUNT(n.id)
                                   FROM Knygmainys\UserBundle\Entity\NotificationUser n
                                   WHERE n.notification = :notificationId')
                            ->setParameter('notificationId', $id);
                $count = $query->getSingleScalarResult();

                if ($count == 1) {
                    $notificationRepository = $em->getRepository('KnygmainysUserBundle:Notification');
                    $notification = $notificationRepository->findOneBy(array('id' => $id));

                    $em->remove($notifiedUser);
                    $em->remove($notification);
                    $em->flush();
                } else if ($count > 1) {
                    $em->remove($notifiedUser);
                    $em->flush();
                }

                $response = json_encode(array('message' => 'Pranešimas ištrintas'));
            } else {
                $response = json_encode(array('message' => 'Jūs neturite priegos!'));
            }

            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));
        } else {
            $response = json_encode(array('message' => 'Jūs neturite priegos!'));

            return new Response($response, 400, array(
                'Content-Type' => 'application/json'
            ));
        }
    }

    public function updateStatusAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = intval(strip_tags($request->request->get('id')));

            $em = $this->getDoctrine()->getManager();

            $repository = $em->getRepository('KnygmainysUserBundle:NotificationUser');
            $notifiedUser = $repository->findOneBy(array('notification'=> $id, 'user'=> $this->getUser()->getId() ));
            if($notifiedUser != null)
            {
                $notifiedUser->setSeen(true);
                $em->persist($notifiedUser);
                $em->flush();

                $response = json_encode(array('message' => 'Pranešimo statusas pakeistas!'));
            }else{
                $response = json_encode(array('message' => 'Jūs neturite priegos!'));
            }

            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));
        }else{
            $response = json_encode(array('message' => 'Jūs neturite priegos!'));

            return new Response($response, 400, array(
                'Content-Type' => 'application/json'
            ));
        }
    }

}