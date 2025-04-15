<?php

namespace App\Normalizer;

use App\Entity\DragonTreasure;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
#[AsDecorator('api_platform.jsonld.normalizer.item')]
class AddOwnerGroupsNormalizer implements NormalizerInterface, SerializerAwareInterface {


    public function __construct(private NormalizerInterface $normalizer, private Security $security)
    {
        
    }


    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        if($object instanceof DragonTreasure && $this->security->getUser() === $object->getOwner()){
            $context['groups'][] = 'owner:read';
        }

        $normalized = $this->normalizer->normalize($object, $format, $context);

        if($object instanceof DragonTreasure && $this->security->getUser() === $object->getOwner()){
            $context['isMine'] = true;
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->normalizer->supportsNormalization($data, $format);
    }

    public function setSerializer(SerializerInterface $serializer){
        if($this->normalizer instanceof SerializerAwareInterface){
            $this->normalizer->setSerializer($serializer);
        }
    }

}