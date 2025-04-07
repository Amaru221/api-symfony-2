<?php

use Zenstruck\Browser\Json;
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
        ->json();
        
        
        
        $json->assertMatches('keys("hydra:member"[0])',[
            '@id',
            '@type',
            'name',
            'description',
            'owner',
            'value',
            'coolFactor',
            'owner',
            'shortDescription',
            'plunderedAtAgo',
        ]);
    }


}