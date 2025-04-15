<?php

namespace App\ApiPlatform;

use ApiPlatform\GraphQl\Serializer\SerializerContextBuilder;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;



class AdminGroupsContextBuilder extends SerializerContextBuilderInterface {

    public function __construct(private SerializerContextBuilder $decorated)
    {
        
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array{
        //TODO

    }

}