<?php

namespace App\ApiPlatform;

use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.serializer.context_builder')]
class AdminGroupsContextBuilder implements SerializerContextBuilderInterface {

    public function __construct(private SerializerContextBuilderInterface $decorated, private Security $security)
    {
        
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array{
        //TODO
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        dump('I AM WORKING!');

        if(isset($context['groups']) && $this->security->isGranted('ROLE_ADMIN')){
            $context['groups'][] = $normalization ? 'admin:read' : 'admin:write';
        }

        return $context;
    }

}