<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage',methods:['GET'])]
    public function index(): Response
    {
        if(!$this->getUser()){
            return new RedirectResponse('login');
        }
        return $this->render('default/index.html.twig');
    }
}
