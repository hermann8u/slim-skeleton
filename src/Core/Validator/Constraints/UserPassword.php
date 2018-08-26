<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserPassword extends Constraint
{
    public $message = "Bad credentials";

    public function validatedBy()
    {
        return 'validator.user_password';
    }
}