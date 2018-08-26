<?php

namespace Core\Security;

use App\Entity\User;
use Core\Database\Hydration;
use Core\Database\Manager;
use Firebase\JWT\JWT;
use Slim\Http\Request;

class Auth
{
    const ALGORITHM = 'HS256';

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $tokenLifeTime;

    /**
     * @param Manager $manager
     * @param string $url
     * @param string $secret
     * @param string $tokenLifeTime
     */
    public function __construct(Manager $manager, string $url, string $secret, string $tokenLifeTime = '2 hours')
    {
        $this->manager = $manager;
        $this->url = $url;
        $this->secret = $secret;
        $this->tokenLifeTime = $tokenLifeTime;
    }

    /**
     * Generate a new JWT token
     *
     * @param User $user
     * @return string
     * @throws \Exception
     */
    public function generateToken(User $user)
    {
        $now = new \DateTime();
        $future = new \DateTime("now +".$this->tokenLifeTime);

        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "jti" => base64_encode(random_bytes(16)),
            'iss' => $this->url,  // Issuer
            "sub" => $user->getId(),
        ];

        $secret = $this->secret;
        $token = JWT::encode($payload, $secret, self::ALGORITHM);

        return $token;
    }

    /**
     * Attempt to find the user based on email and verify password
     *
     * @param $email
     * @param $password
     * @return bool|User
     * @throws \Core\Exception\BadRepositoryClass
     * @throws \Core\Exception\ClassNotFoundException
     */
    public function attempt($email, $password)
    {
        if (!$user = $this->manager->getRepository(User::class, Hydration::AS_ENTITY)->findByEmail($email)) {
            return false;
        }

        if (!password_verify($password, $user->getPassword())) {
            return false;
        }

        return $user;
    }

    /**
     * Retrieve a user by the JWT token from the request
     *
     * @param \Slim\Http\Request $request
     *
     * @return User|false
     *
     * @throws \Core\Exception\BadRepositoryClass
     * @throws \Core\Exception\ClassNotFoundException
     */
    public function getRequestUser(Request $request)
    {
        if (!$request->getHeader("Authorization")) {
            return null;
        }

        $token = explode(" ", $request->getHeaderLine("Authorization"))[1];
        $decodedToken = JWT::decode($token, $this->secret, [self::ALGORITHM]);

        return $this->manager->getRepository(User::class, Hydration::AS_ENTITY)->find($decodedToken->sub);
    }
}