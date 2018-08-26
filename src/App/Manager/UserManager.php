<?php

namespace App\Manager;

use App\Entity\User;
use Core\Database\Manager;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserManager
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(Manager $manager, ValidatorInterface $validator)
    {
        $this->manager = $manager;
        $this->validator = $validator;
    }

    public function getUserProfile($user)
    {
        $query = $this->manager->getRepository(User::class)->findFullUserInfo($user);

        dump($query);die;
    }

    /**
     * Change the password for the given user and update him. If no password is given, generate a random one.
     *
     * @param User $user
     * @param string|null $password
     * @return bool|string
     */
    public function changePassword(User &$user, string $password = null)
    {
        if (!$password) {
            $password = substr(str_shuffle(strtolower(sha1(rand() . time() . "Salt string"))),0, 8);
        }

        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        $this->manager->save($user);

        return $password;
    }

    /**
     * Validate a password
     *
     * @param string $pwd
     *
     * @return ConstraintViolationListInterface
     */
    public function validatePassword(string $pwd)
    {
        return $this->validator->validate($pwd, [
            new Assert\NotBlank([
                'message' => 'validation_errors.password.empty'
            ]),
            new Assert\Length([
                'minMessage' => 'validation_errors.password.too_short',
                'min' => 6
            ])
        ]);
    }
}