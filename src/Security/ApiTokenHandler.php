<?php

use App\Repository\ApiTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandler extends AccessTokenHandlerInterface
{

    public function __construct(private ApiTokenRepository $apiTokenRepository){
    
    }
    
    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge{
        //TODO
        $token = $this->apiTokenRepository->findOneBy(['token' => $accessToken]);

        if(!$token or !$token->isValid()){
            throw new BadCredentialsException();
        }

        return new UserBadge($token->getOwnedBy()->getUserIdentifier());
    }

}