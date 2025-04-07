<?php

use Zenstruck\Browser\Json;
use App\Factory\UserFactory;
use Zenstruck\Browser\HttpOptions;
use App\Factory\DragonTreasureFactory;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DragonTreasureResourceTest extends KernelTestCase {

    use ResetDatabase;
    use HasBrowser;


    public function testGetCollectionOfTreasures(): void {
        DragonTreasureFactory::createMany(5);

        $json = $this->browser()
        ->get('/api/treasures')
        ->assertJson()
        ->assertJsonMatches('"hydra:totalItems"', 5)
        ->assertJsonMatches('length("hydra:member")', 5)
        ->json()
        ;
        $this->assertSame(array_keys($json->decoded()['hydra:member'][0]), [
            '@id',
            '@type',
            'name',
            'description',
            'value',
            'coolFactor',
            'owner',
            'shortDescription',
            'plunderedAtAgo',
        ]);
    }

    public function testPostToCreateTreasure(): void {
        $user = UserFactory::createOne();

        $this->browser()
        ->actingAs($user)
        ->post('/api/treasures', [
            'json' => [],
        ])
        ->assertStatus(422)
        ->post('/api/treasures', HttpOptions::json([
                'name' => 'Test Dragon Treasure',
                'description' => 'A test dragon treasure.',
                'value' => 1000,
                'coolFactor' => 5,
                'owner' => '/api/users/'.$user->getId(),
        ])->withHeader('Accept', 'application/ld+json'))
        ->assertStatus(201)
        ->assertJsonMatches('name', 'Test Dragon Treasure')
        ->dump()
        ;
    }


}