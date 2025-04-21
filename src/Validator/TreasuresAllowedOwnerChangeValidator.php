<?php

namespace App\Validator;

use App\Entity\DragonTreasure;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TreasuresAllowedOwnerChangeValidator extends ConstraintValidator
{

    public function __construct(private EntityManager $entityManager)
    {

    }

    public function validate($value, Constraint $constraint)
    {
        assert($constraint instanceof TreasuresAllowedOwnerChange);

        if (null === $value || '' === $value) {
            return;
        }

        assert($value instanceof Collection);

        $unitOfWork = $this->entityManager->getUnitOfWork();

        foreach($value as $dragonTreasure) {
            assert($dragonTreasure instanceof DragonTreasure);

            $originalData = $unitOfWork->getOriginalEntityData($dragonTreasure);
            dd($originalData, $dragonTreasure);
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
