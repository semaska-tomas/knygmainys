<?php

namespace Knygmainys\BooksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KnygmainysBooksBundle:Default:index.html.twig');
    }
}
