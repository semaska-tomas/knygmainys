<?php

namespace Knygmainys\CrawlerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KnygmainysCrawlerBundle:Default:index.html.twig');
    }
}
