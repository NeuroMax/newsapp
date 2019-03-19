<?php
namespace App;

use App\Services\Router\Route;
use App\Services\Router\Request;

/**
 * Базовый класс модуля
 * Class Module
 * @package App
 */
abstract class Module
{
    /** @var string  */
    private $name = '';

    /**
     * Module constructor.
     * @param string $name
     */
    function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * Функция вызывает экшен в контролле определенный в маршруте
     * @param Route $route
     * @throws \Exception
     */
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

    /**
     * @return mixed
     */
    abstract public function load ();

    /**
     * @return mixed
     */
    abstract protected function registerRoutes ();
}