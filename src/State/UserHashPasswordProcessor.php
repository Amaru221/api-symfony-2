<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
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
