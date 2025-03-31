<?php
namespace App\Controller;

use Exception;
use App\Entity\User;
use ApiPlatform\Api\IriConverterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController {

    #[Route('/')]
    public function homepage(NormalizerInterface $normalizer, #[CurrentUser] User $user = null) :Response{
        
        return $this->render('main/homepage.html.twig', [
            'userData' => $normalizer->normalize($user, 'jsonld',[
                'groups' => ['user:read']
            ])
        ]);
    }
    
    #[Route('/login', name: 'app_login', methods: ["POST"])]
    public function login(IriConverterInterface $iriConverter, #[CurrentUser] $user = null) :Response {


        if(!$user){
            return $this->json([
                'error' => 'Invalid login request: check the Content-Type header is application-json'

            ], 401);    
        }

        return new Response(null, 204, [
            'Location' => $iriConverter->getIriFromResource($user),
        ]);

    }


    #[Route('/logout', name: 'app_logout')]
    public function logout() :void {
        throw new Exception('This should never be reached');
    }

    
}