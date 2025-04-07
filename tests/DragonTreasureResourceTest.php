<?php

use Zenstruck\Browser\Json;
use App\Factory\DragonTreasureFactory;
use App\Factory\UserFactory;
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
        $user = UserFactory::createOne(['password' => 'pass']);

        $this->browser()
        ->post('/login', [
            'json' => [
                'email' => $user->getEmail(),
                'password' => 'pass',
            ],
        ])
        ->assertStatus(204)
        ->post('/api/treasures', [
            'json' => [],
        ])
        ->assertStatus(422)
        ->dump()
        ;
    }


}