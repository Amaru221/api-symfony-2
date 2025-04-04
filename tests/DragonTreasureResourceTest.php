<?php

use Zenstruck\Browser\Test\HasBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DragonTreasureResourceTest extends KernelTestCase {

    use HasBrowser;

    public function testGetCollectionOfTreasures(): void {
        $this->browser()
        ->get('/api/treasures')
        ->dump();
    }


}