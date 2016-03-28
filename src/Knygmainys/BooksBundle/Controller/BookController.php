<?php

namespace Knygmainys\BooksBundle\Controller;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\BooksBundle\Entity\Release;
use Knygmainys\BooksBundle\Form\MergedBookReleaseFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    public function indexAction()
    {
        echo 'asd ';
        return $this->render('KnygmainysBooksBundle:Default:index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $customErrors = [];
        $book = new Book();
        $release = new Release();
        $formData['book'] = $book;
        $formData['release']  = $release;

        $form = $this->createForm(new MergedBookReleaseFormType(), $formData);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {

        }

        return $this->render('KnygmainysBooksBundle:Book:create.html.twig', [
            'form' => $form->createView(),
            'customErrors' => $customErrors
        ]);
    }

    public function searchBookTitlesAction()
    {

    }
}
