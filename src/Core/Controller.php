<?php

namespace Core;

use App\Entity\User;
use Core\Exception\ParameterNotFound;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class Controller
{
    /**
     * @var \Core\Database\Manager
     */
    protected $manager;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @var \Slim\Views\Twig
     */
    protected $view;

    /**
     * @var JsonResponseFormatter
     */
    private $jsonResponseFormatter;

    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $settings;

    /**
     * Controller constructor.
     *
     * Don't overwrite this method to not expose the container directly. If you want to get a service from the container,
     * use the get method instead or init your dependencies with the overwritten init method.
     *
     * @param \Interop\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->manager = $this->get('manager');
        $this->logger = $this->get('logger');
        $this->view = $this->get('view');
        $this->settings = $this->get('settings');
        $this->jsonResponseFormatter = $this->get('json_response_formatter');

        $this->logger->addInfo('Matching route: '.$this->get('request')->getUri()->getPath());

        $this->init();
    }

    /**
     * This function is used to init the controller dependencies without exposing the container directly.
     * Override it and init them with the get method.
     */
    public function init()
    {
    }

    /**
     * Shortcut for getting services from the container
     *
     * @param string $service
     *
     * @return mixed
     */
    public function get(string $service)
    {
        if ($this->container) {
            return $this->container[$service];
        }

        return null;
    }

    /**
     * Get a setting from the app settings. The name is the arrays key split by a dot.
     * This method only get the settings define under the app key to not expose sensible settings.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws ParameterNotFound
     */
    public function getSetting(string $name)
    {
        $explodeName = explode('.', $name);
        $settings = $this->settings['app'];

        foreach ($explodeName as $key) {
            if (isset($settings[$key])) {
                $settings = $settings[$key];
            }
            else {
                throw new ParameterNotFound("Undefined parameter \"$name\" at \"$key\"");
            }
        }

        return $settings;
    }

    /**
     * Get current logged user
     *
     * @param Request $request
     *
     * @return User|null
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getUser(Request $request)
    {
        return $this->get('auth')->getRequestUser($request);
    }

    /**
     * Shortcut for Twig render method
     *
     * @param ResponseInterface $response The Slim response object
     * @param string $template The template path
     * @param array $parameters The parameters for the twig file
     *
     * @return ResponseInterface
     */
    public function render(ResponseInterface $response, string $template, array $parameters)
    {
        return $this->view->render($response, $template, $parameters);
    }

    /**
     * Shortcut to prepare to return a json success Response
     *
     * @param Response $response
     * @param $data
     *
     * @return Response
     */
    public function jsonSuccessResponse(Response $response, $data)
    {
        return $this->jsonResponseFormatter->successResponse($response, $data);
    }

    /**
     * Shortcut to prepare to return a json error Response
     *
     * @param Response $response
     * @param $code
     * @param $errors
     *
     * @return Response
     */
    public function jsonErrorResponse(Response $response, $code, $errors)
    {
        return $this->jsonResponseFormatter->errorResponse($response, $code, $errors);
    }
}