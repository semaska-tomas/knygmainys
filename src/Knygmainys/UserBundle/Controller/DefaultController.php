<?php

namespace Knygmainys\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KnygmainysUserBundle:Default:index.html.twig');
    }
}
