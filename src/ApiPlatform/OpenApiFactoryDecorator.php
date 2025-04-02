<?php
namespace App\ApiPlatform;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('')]
class OpenApiFactoryDecorator implements OpenApiFactoryInterface {

    public function __construct(private OpenApiFactoryInterface $decorated){

    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        return $openApi;
    }
}