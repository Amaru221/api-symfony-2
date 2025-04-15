<?php

namespace App\ApiPlatform;

use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;



class AdminGroupsContextBuilder extends SerializerContextBuilderInterface {

    

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array{
        //TODO

    }

}