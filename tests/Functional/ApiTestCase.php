<?php

namespace App\Tests\Functional;

use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/** esta clase reemplaza el metodo browser de kernelTestCase para añadir nuestra propia opcion de cabecera */
abstract class ApiTestCase extends KernelTestCase {

    use HasBrowser {
        browser as baseKernelBrowser;
    }

    /** añadimos la cabecera accept, a application/ld+json para que solicite siempre este formato */
    protected function browser(array $options = [], array $server = []) {
        return $this->baseKernelBrowser($options, $server)
            ->setDefaultHttpOptions(
                HttpOptions::create()
                    ->withHeader('Accept', 'application/ld+json')
            );
    }

}