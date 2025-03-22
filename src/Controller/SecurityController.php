<?php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController {



    public function login() :Response {
        return $this->render('security/login.html.twig');
    }

    
}