<?php

namespace Knygmainys\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KnygmainysAdminBundle:Default:index.html.twig');
    }
}
