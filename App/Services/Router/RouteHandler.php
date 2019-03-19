<?php
namespace App\Services\Router;


class RouteHandler
{
    /** @var Router */
    private $router;
    /** @var string  */
    private $alias = '';

    function __construct(Router $router, string $alias = '')
    {
        $this->router = $router;
        if (!empty($alias)) $this->alias = $alias;
    }

    public function GET (string  $path, string $module, string $controllerAction)
    {
        $route = new Route(Request::METHOD_GET, $this->alias.$path, $module, $controllerAction);
        $this->router->registerRoute($route);
    }

    public function POST (string  $path, string $module, string $controllerAction)
    {
        $route = new Route(Request::METHOD_POST, $this->alias.$path, $module, $controllerAction);
        $this->router->registerRoute($route);
    }

    public function PUT (string  $path, string $module, string $controllerAction)
    {
        $route = new Route(Request::METHOD_PUT, $this->alias.$path, $module, $controllerAction);
        $this->router->registerRoute($route);
    }

    public function DELETE (string  $path, string $module, string $controllerAction)
    {
        $route = new Route(Request::METHOD_DELETE, $this->alias.$path, $module, $controllerAction);
        $this->router->registerRoute($route);
    }
}