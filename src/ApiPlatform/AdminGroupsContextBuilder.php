<?php

namespace App\ApiPlatform;

use ApiPlatform\GraphQl\Serializer\SerializerContextBuilder;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.serializer.context_builder')]
class AdminGroupsContextBuilder extends SerializerContextBuilderInterface {

    public function __construct(private SerializerContextBuilderInterface $decorated)
    {
        
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array{
        //TODO
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        dump('I AM WORKING!');

        return $context;
    }

}