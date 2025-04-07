<?php

use App\Factory\UserFactory;
use Zenstruck\Browser\HttpOptions;
use App\Tests\Functional\ApiTestCase;
use App\Factory\DragonTreasureFactory;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Browser\Test\HasBrowser;

class DragonTreasureResourceTest extends ApiTestCase {

    use ResetDatabase;


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
        ->post('/api/treasures',[
            'json' => 
            [
                'name' => 'Test Dragon Treasure',
                'description' => 'A test dragon treasure.',
                'value' => 1000,
                'coolFactor' => 5,
                'owner' => '/api/users/'.$user->getId(),
            ]
        ])
        ->assertStatus(201)
        ->assertJsonMatches('name', 'Test Dragon Treasure')
        ;
    }


}