<?php
namespace App\Controller;

use ApiPlatform\Symfony\Routing\IriConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SecurityController extends AbstractController {

    #[Route('/login', name: 'app_login', methods: ["POST"])]
    public function login(IriConverter $iriConverter, #[CurrentUser] $user = null) :Response {

        return new Response(null, 204, [
            'Location' => $iriConverter->getIriFromResource($user),
        ]);

        if(!$user){
            return $this->json([
                'error' => 'Invalid login request: check the Content-Type header is application-json'

            ], 401);    
        }

        return $this->json([
            'user' => $user->getId(),
        ]);
    }

    
}