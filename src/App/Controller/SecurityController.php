<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use Core\Controller;
use Core\Security\Auth;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends Controller
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function init()
    {
        $this->auth = $this->get('auth');
        $this->userManager = $this->get('app.manager.user');
        $this->validator = $this->get('validator');
    }

    public function register(Request $request, Response $response)
    {
        $userData = $request->getParam('user');

        $user = new User();
        $user
            ->setName($userData['name'])
            ->setEmail($userData['email'])
        ;

        $errors = $this->validator->validate($user);
        $errors->addAll($this->userManager->validatePassword($userData['password'], isset($userData['verify']) ? $userData['verify'] : null));

        if (count($errors)) {
            return $this->jsonErrorResponse($response, 400, $errors);
        }

        $this->userManager->changePassword($user, $userData['password']);

        return $this->jsonSuccessResponse($response, [
            'user' => $user->toArray()
        ]);
    }

    public function login(Request $request, Response $response)
    {
        $userData = $request->getParam('user');

        if (!$user = $this->auth->attempt($userData['email'], $userData['password'])) {
            return $this->jsonErrorResponse($response, 400, 'validation_errors.user.bad_credentials');
        }

        $token = $this->auth->generateToken($user);

        $this->userManager->getUserProfile($user);

        return $this->jsonSuccessResponse($response, [
            'user' => $user->toArray(),
            'token' => $token
        ]);
    }

    public function facebookLogin(Request $request, Response $response)
    {
        $userData = $request->getParam('user');

        $user = $this->manager->getRepository(User::class)->findOneBy([
            'facebookId' => $userData['fb_id']
        ]);

        if (!$user) {
            $user = $this->manager->getRepository(User::class)->findOneBy([
                'email' => $userData['email']
            ]);

            if ($user) {
                $user->setFacebookId($userData['fb_id']);

                $this->manager->save($user);
            }
            else {
                $user = new User();
                $user
                    ->setName($userData['name'])
                    ->setEmail($userData['email'])
                    ->setFacebookId($userData['fb_id'])
                ;

                $this->userManager->changePassword($user);
            }
        }

        return $this->jsonSuccessResponse($response, [
            'user' => $user->toArray(),
            'token' => $this->auth->generateToken($user)
        ]);
    }

    public function changePassword(Request $request, Response $response)
    {
        $user = $this->getUser($request);
        $this->userManager->changePassword($user, $request->getParam('new_password'));

        return $this->jsonSuccessResponse($response, [
            'token' => $this->auth->generateToken($user)
        ]);
    }
}