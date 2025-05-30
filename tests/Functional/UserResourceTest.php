<?php

namespace App\Tests\Functional;

use App\Entity\DragonTreasure;
use App\Factory\DragonTreasureFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserResourceTest extends ApiTestCase {

    use ResetDatabase;

    public function testPostToCreateUser(): void {
        $this->browser()
            ->post('/api/users', [
                'json' => [
                    'email' => 'draggin_in_the_morning@coffe.com',
                    'username' => 'draggin_in_the_morning',
                    'password' => 'password',
                ]
            ])
            ->assertStatus(201)
            ->post('/login', [
                'json' => [
                    'email' => 'draggin_in_the_morning@coffe.com',
                    'password' => 'password',
                ]
            ])
            ->assertSuccessful()
        ;
    }

    public function testPatchToUpdateUser(): void {

        $user = UserFactory::createOne();

        $this->browser()
            ->actingAs($user)
            ->patch('/api/users/'.$user->getId(), [
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
                'json' => [
                    'username' => 'changed',
                ]
            ])
            ->assertStatus(200)
        ;

    }

    public function testTreasuresCannotBeStolen(): void
    {
        $user = UserFactory::createOne();
        $otherUser = UserFactory::createOne();
        $dragonTreasure = DragonTreasureFactory::createOne(['owner' => $otherUser]);

        $this->browser()
        ->actingAs($user)
        ->patch('/api/users/'. $user->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'username' => 'changed',
                'dragonTreasures' => [
                    '/api/treasures/'. $dragonTreasure->getId(),
                ],
            ]
        ])
        ->assertStatus(422);

    }

    public function testUnpublishedTreasuresNotReturned(): void {
        $user = UserFactory::createOne();
        DragonTreasureFactory::createOne([
            'isPublished' => false,
            'owner' => $user,
        ]);

        $this->browser()
        ->actingAs(UserFactory::createOne())
        ->get('/api/users/'. $user->getId())
        ->assertJsonMatches('length("dragonTreasures")', 0)
        ;

    }


}