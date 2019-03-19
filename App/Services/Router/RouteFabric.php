<?php
namespace App\Services\Router;


/**
 * Класс - фабрика для создания маршрутов
 * Class RouteHandler
 * @package App\Services\Router
 */
class RouteFabric
{
    /** @var Router */
    private $router;
    /** @var string  */
    private $alias = '';

    /**
     * RouteHandler constructor.
     * @param Router $router
     * @param string $alias
     */
    function __construct(Router $router, string $alias = '')
    {
        $this->router = $router;
        if (!empty($alias)) $this->alias = $alias;
    }

    /**
     * @param string $path Маршрут (например '/admin/news/:id') параметр писать с двоеточием
     * @param string $module Название модуля
     * @param string $controllerAction Название контроллера и экшена вида 'UserController:index'
     * @param array $options Опции (из опций доступно [ 'auth' => true ] Разрешение маршрута только авторизированным пользователям)
     * @throws \Exception
     */
    public function GET (string  $path, string $module, string $controllerAction, array $options = [])
    {
        $route = new Route(Request::METHOD_GET, $this->alias.$path, $module, $controllerAction, $options);
        $this->router->registerRoute($route);
    }

    /**
     * @param string $path Маршрут (например '/admin/news/:id') параметр писать с двоеточием
     * @param string $module Название модуля
     * @param string $controllerAction Название контроллера и экшена вида 'UserController:index'
     * @param array $options Опции (из опций доступно [ 'auth' => true ] Разрешение маршрута только авторизированным пользователям)
     * @throws \Exception
     */
    public function POST (string  $path, string $module, string $controllerAction, array $options = [])
    {
        $route = new Route(Request::METHOD_POST, $this->alias.$path, $module, $controllerAction, $options);
        $this->router->registerRoute($route);
    }

    /**
     * @param string $path Маршрут (например '/admin/news/:id') параметр писать с двоеточием
     * @param string $module Название модуля
     * @param string $controllerAction Название контроллера и экшена вида 'UserController:index'
     * @param array $options Опции (из опций доступно [ 'auth' => true ] Разрешение маршрута только авторизированным пользователям)
     * @throws \Exception
     */
    public function PUT (string  $path, string $module, string $controllerAction, array $options = [])
    {
        $route = new Route(Request::METHOD_PUT, $this->alias.$path, $module, $controllerAction, $options);
        $this->router->registerRoute($route);
    }

    /**
     * @param string $path Маршрут (например '/admin/news/:id') параметр писать с двоеточием
     * @param string $module Название модуля
     * @param string $controllerAction Название контроллера и экшена вида 'UserController:index'
     * @param array $options Опции (из опций доступно [ 'auth' => true ] Разрешение маршрута только авторизированным пользователям)
     * @throws \Exception
     */
    public function DELETE (string  $path, string $module, string $controllerAction, array $options = [])
    {
        $route = new Route(Request::METHOD_DELETE, $this->alias.$path, $module, $controllerAction, $options);
        $this->router->registerRoute($route);
    }
}