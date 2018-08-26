<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Core\AbstractEntity;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Core\Validator\Constraints as CoreAssert;

/**
 * @CoreAssert\UniqueValue(message="validation_errors.email.already_exist", property="email")
 */
class User extends AbstractEntity
{
    const TABLE_NAME = 'app_user';
    //const REPOSITORY = UserRepository::class;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="validation_errors.name.empty")
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\Email(message="validation_errors.email.invalid")
     * @Assert\NotBlank(message="validation_errors.email.empty")
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * Define the relation between properties and database table columns.
     * The key is the property name and the value is the column name.
     *
     * @return array
     */
    public static function columnsDefinition()
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::INTEGER
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::STRING
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::STRING
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::STRING
            ],
            'createdAt' => [
                'name' => 'created_at',
                'type' => Type::DATETIME
            ]
        ];
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'createdAt' => $this->createdAt,
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = strtolower($email);

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt(\DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}