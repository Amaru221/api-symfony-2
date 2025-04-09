<?php
namespace App\ApiPlatform;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\SecurityScheme;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

/** decorador para ApiFactory */
#[AsDecorator('api_platform.openapi.factory')]
class OpenApiFactoryDecorator implements OpenApiFactoryInterface {

    public function __construct(private OpenApiFactoryInterface $decorated){

    }

    /** añadimos al esquema access_token que sea tipo http y incluya bearer antes del token */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $securitySchemes = $openApi->getComponents()->getSecuritySchemes() ? : new \ArrayObject();

        $securitySchemes['access_token'] = new SecurityScheme(
            type: 'http',
            scheme: 'bearer',
        );

        return $openApi;
    }
}