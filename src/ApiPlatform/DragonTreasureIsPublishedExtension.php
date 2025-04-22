<?php
namespace App\ApiPlatform;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\DragonTreasure;

class DragonTreasureIsPublishedExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface{

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if(DragonTreasure::class !== $resourceClass){
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(sprintf('%s.isPublished = :isPublished', $rootAlias))
        ->setParameter('isPublished', true);

    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {

        $this->addIsPublishedWhere($queryBuilder, $resourceClass);

    }

    public function addIsPublishedWhere(QueryBuilder $queryBuilder, string $resourceClass) {
        if(DragonTreasure::class !== $resourceClass){
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.isPublished = :isPublished', $rootAlias))
        ->setParameter('isPublished', true);
    }

}