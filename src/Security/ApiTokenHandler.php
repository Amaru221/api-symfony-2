<?php
namespace App\Security;

use App\Repository\ApiTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/** estac clase controla el funcionamiento de access_control*/
class ApiTokenHandler implements AccessTokenHandlerInterface
{

    public function __construct(private ApiTokenRepository $apiTokenRepository){
    
    }
    
    /** devuelve el userBadge objeto con los datos del usuario autenticado, si no, lanza una excepcion  */
    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge{
        //TODO
        $token = $this->apiTokenRepository->findOneBy(['token' => $accessToken]);

        if(!$token){
            throw new BadCredentialsException();
        }

        if(!$token->isValid()){
            throw new CustomUserMessageAuthenticationException("Token expired");
        }

        $token->getOwnedBy()->markAsTokenScopes($token->getScopes());

        return new UserBadge($token->getOwnedBy()->getUserIdentifier());
    }

}