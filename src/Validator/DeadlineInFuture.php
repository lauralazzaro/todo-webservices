<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class DeadlineInFuture extends Constraint
{
    public string $message = 'The deadline must be today or in the future.';
}
