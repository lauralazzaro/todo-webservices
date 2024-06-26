<?php

namespace App\Validator;

use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeadlineInFutureValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /* @var $constraint DeadlineInFuture */

        $now = new DateTime();
        if ($value < $now->setTime(0, 0, 0)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
