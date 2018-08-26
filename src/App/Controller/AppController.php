<?php

namespace App\Controller;

use Core\Controller;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;
use Slim\Views\Twig;

class AppController extends Controller
{
    /**
     * @var Twig
     */
    private $view;

    public function init()
    {
        $this->view = $this->get('view');
    }

    public function index(Request $request, Response $response)
    {
        $routes = $this->get('router')->getRoutes();
        $formattedRoutes = [];

        $isLogged = (boolean) $this->getUser($request);
        $noAuthRoutes = $this->getParameter('jwt.ignore');

        /**
         * @var Route $route
         */
        foreach ($routes as $route) {
            if ($isLogged || in_array($route->getPattern(), $noAuthRoutes)) {
                $groups = '';
                foreach ($route->getGroups() as $group) {
                    $groups .= $group->getPattern();
                }

                if ($groups[0] === '/') {
                    $groups = substr($groups, 1);
                }

                $groups = str_replace('/', ' ', $groups);

                $formattedRoutes[$groups][$route->getName()] = [
                    'name' => $route->getName(),
                    'methods' => $route->getMethods(),
                    'pattern' => $route->getPattern()
                ];
            }
        }

        if ($request->isGet()) {
            return $this->view->render($response, 'app/index.html.twig', [
                'groups' => $formattedRoutes,
                'isLogged' => $isLogged
            ]);
        }

        return $this->jsonSuccessResponse($response, $formattedRoutes);
    }
}