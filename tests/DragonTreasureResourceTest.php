<?php

use App\Factory\DragonTreasureFactory;
use Zenstruck\Browser\Test\HasBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class DragonTreasureResourceTest extends KernelTestCase {

    use ResetDatabase;
    use HasBrowser;


    public function testGetCollectionOfTreasures(): void {
        DragonTreasureFactory::createMany(5);
        $this->browser()
        ->get('/api/treasures')
        ->dump()
        ->assertJson()
        ->assertJsonMatches('"hydra:totalItems"', 5)
        ;
    }


}