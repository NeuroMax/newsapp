<?php
namespace App;

use App\Services\Router\Route;
use App\Services\Router\Request;

abstract class Module
{
    /** @var string  */
    private $name = '';

    function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName ()
    {
        return $this->name;
    }

    public function dispatch(Route $route)
    {
        $name = $this->getName();
        $controller = "\\App\\Modules\\$name\\Controllers\\".$route->getController();
        $action = $route->getAction();

        if (!class_exists($controller) && !method_exists($controller, $action))
            throw new \Exception('Controller or action not exist');

        $controllerObj = new $controller();
        $controllerObj->$action(new Request($route));
    }

    abstract public function load ();
    abstract protected function registerRoutes ();
}