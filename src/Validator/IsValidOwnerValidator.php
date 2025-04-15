<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidOwnerValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        assert($constraint instanceof IsValidOwner);

        if (null === $value || '' === $value) {
            return;
        }

        assert($$value instanceof User);


        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
