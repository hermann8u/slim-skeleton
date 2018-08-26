<?php

namespace Core\Validator\Constraints;

use Core\Database\Manager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

class UniqueValueValidator extends ConstraintValidator
{
    /**
     * @var Manager
     */
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @throws \Core\Exception\BadRepositoryClass
     * @throws \Core\Exception\ClassNotFoundException
     */
    public function validate($value, Constraint $constraint)
    {
        $repository = $this->manager->getRepository(get_class($value));

            $getter = 'get'.ucfirst($constraint->property);

            $foundEntity = $repository->findOneBy([
                $constraint->property => $value->{$getter}()
            ]);

            if ($foundEntity) {
                $this->context->buildViolation($constraint->message, [
                        '{{ value }}' => $value->{$getter}(),
                        '{{ property }}' => $constraint->property
                    ])
                    ->atPath($constraint->property)
                    ->addViolation()
                ;
            }
    }
}