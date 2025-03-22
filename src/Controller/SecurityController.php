<?php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController {

    #[Route('/login', name: 'app_login', methods: ["POST"])]
    public function login() :Response {



        return $this->render('security/login.html.twig');
    }

    
}