<?php

namespace Core\Validator\Constraints;

use Core\Security\Auth;
use Slim\Http\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserPasswordValidator extends ConstraintValidator
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Auth
     */
    private $auth;

    public function __construct(Request $request, Auth $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public function validate($value, Constraint $constraint)
    {
        $user = $this->auth->getRequestUser($this->request);

        if (!$user) {
            throw new \LogicException('No user authenticate');
        }

        if (!password_verify($value, $user->getPassword())) {
            $this->context->buildViolation($constraint->message, [
                    '{{ value }}' => $value
                ])
                ->addViolation()
            ;
        }
    }
}