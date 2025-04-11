<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class UserHashPasswordProcessor implements ProcessorInterface
{

    public function __construct(private ProcessorInterface $innerProcessor)
    {
        
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // Handle the state
        dump("is ALIVE!");

        $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
