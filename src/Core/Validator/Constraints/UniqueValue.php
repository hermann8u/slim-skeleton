<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueValue extends Constraint
{
    public $message = "The value \"{{ value }}\" for \"{{ property }}\" already exist";

    public $property;

    public function validatedBy()
    {
        return 'validator.unique_value';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getRequiredOptions()
    {
        return [
            'property'
        ];
    }
}