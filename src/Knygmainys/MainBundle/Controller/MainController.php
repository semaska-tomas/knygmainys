<?php

namespace Knygmainys\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('KnygmainysMainBundle:Main:index.html.twig');
    }
}
