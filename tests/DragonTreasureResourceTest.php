<?php

namespace App\Tests\Functional;

use App\Entity\ApiToken;
use App\Entity\DragonTreasure;
use App\Factory\ApiTokenFactory;
use App\Factory\UserFactory;
use Zenstruck\Browser\HttpOptions;
use App\Tests\Functional\ApiTestCase;
use App\Factory\DragonTreasureFactory;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Browser\Test\HasBrowser;

class DragonTreasureResourceTest extends ApiTestCase {

    use ResetDatabase;


    public function testGetCollectionOfTreasures(): void {
        DragonTreasureFactory::createMany(5, [
            'isPublished' => true,
        ]);

        DragonTreasureFactory::createOne([
            'isPublished' => false,
        ]);

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


    public function testGetOneOfUnpublishedTreasure404s(): void {

        $dragonTreasure = DragonTreasureFactory::createOne([
            'isPublished' => false,
        ]);

        $json = $this->browser()
        ->get('/api/treasures/'.$dragonTreasure->getId())
        ->assertStatus(404)
        ;
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
                //'owner' => '/api/users/'.$user->getId(),
            ]
        ])
        ->assertStatus(201)
        ->assertJsonMatches('name', 'Test Dragon Treasure')
        ;
    }

    public function testPostToCreateTreasureWithApiKey(): void {

        $token = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_TREASURE_CREATE],
        ]);

        $this->browser()
        ->post('/api/treasures', [
            'json' => [],
            'headers' =>[
                'Authorization' => 'Bearer '.$token->getToken(),
            ]
        ])
        ->dump()
        ->assertStatus(422)
        ;
    
    }


    public function testPatchToUpdateTreasure(){
        $user = UserFactory::createOne();
        $treasure = DragonTreasureFactory::createOne(['owner' => $user]);
        
        $this->browser()
        ->actingAs($user)
        ->patch('/api/treasures/'.$treasure->getId(), [
            'json' => [
                'value' => 12345,
            ]
        ])
        ->assertStatus(200)
        ->assertJsonMatches('value', 12345)
        ;

        $user2 = UserFactory::createOne();
        $this->browser()
        ->actingAs($user2)
        ->patch('/api/treasures/'.$treasure->getId(), [
            'json' => [
                'value' => 6789,
            ]
        ])
        ->assertStatus(403)
        ;

        $this->browser()
        ->actingAs($user)
        ->patch('/api/treasures/'.$treasure->getId(), [
            'json' => [
                'owner' => '/api/users/'.$user2->getId(), 
            ]
        ])
        ->assertStatus(422)
        ;
    }

    public function testPatchUnpublishedWorks(){
        $user = UserFactory::createOne();
        $treasure = DragonTreasureFactory::createOne([
            'owner' => $user,
            'isPublished' => false,
        ]);
        
        $this->browser()
        ->actingAs($user)
        ->patch('/api/treasures/'.$treasure->getId(), [
            'json' => [
                'value' => 12345,
            ]
        ])
        ->assertStatus(200)
        ->assertJsonMatches('value', 12345)
        ;

    }

    public function testAdminCanPatchToEditTreasure(): void {
        $admin = UserFactory::new()->asAdmin()->create();

        $treasure = DragonTreasureFactory::createOne(['isPublished' => true]);

        $this->browser()
        ->actingAs($admin)
        ->patch('/api/treasures/'. $treasure->getId(), [
            'json' => [
                'value' => 12345,
            ]
        ])
        ->assertStatus(200)
        ->assertJsonMatches('value', 12345)
        ->assertJsonMatches('isPublished', true)
        ;
    }

    public function testOwnerCanSeeIsPublishedAndMineField() : void {
        $user = UserFactory::new()->create();
        $treasure = DragonTreasureFactory::createOne([
            'isPublished' => true,
            'owner' => $user,
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/treasures/'. $treasure->getId(), [
                'json' => [
                    'value' => 12345,
                ],
            ])
            ->assertStatus(200)
            ->assertJsonMatches('value', 12345)
            ->assertJsonMatches('isPublished', true)
            ->assertJsonMatches('isMine', true)
        ;
    }


}