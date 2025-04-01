<?php

namespace App\Factory;

use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<ApiToken>
 *
 * @method        ApiToken|Proxy create(array|callable $attributes = [])
 * @method static ApiToken|Proxy createOne(array $attributes = [])
 * @method static ApiToken|Proxy find(object|array|mixed $criteria)
 * @method static ApiToken|Proxy findOrCreate(array $attributes)
 * @method static ApiToken|Proxy first(string $sortedField = 'id')
 * @method static ApiToken|Proxy last(string $sortedField = 'id')
 * @method static ApiToken|Proxy random(array $attributes = [])
 * @method static ApiToken|Proxy randomOrCreate(array $attributes = [])
 * @method static ApiTokenRepository|ProxyRepositoryDecorator repository()
 * @method static ApiToken[]|Proxy[] all()
 * @method static ApiToken[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ApiToken[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static ApiToken[]|Proxy[] findBy(array $attributes)
 * @method static ApiToken[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ApiToken[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class ApiTokenFactory extends PersistentProxyObjectFactory{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return ApiToken::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'ownedBy' => UserFactory::new(),
            'scopes' => [
                ApiToken::SCOPE_TREASURE_CREATE,
                ApiToken::SCOPE_USER_EDIT,
            ],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ApiToken $apiToken): void {})
        ;
    }
}
