<?php
namespace App\Modules\Admin;

use App\Services\Router\RouteHandler;
use App\Services\Router\Router;

class AdminRoutes
{
    /** @var Router */
    private $router;

    function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function register ()
    {
        $handler = new RouteHandler($this->router, '/admin');

//        $handler->GET('/user/list', 'Admin', 'UserController:index');
//        $handler->GET('/user/:id', 'Admin', 'UserController:get_by_id');
//        $handler->GET('/user', 'Admin', 'UserController:create');
//        $handler->POST('/user', 'Admin', 'UserController:create');

        $handler->GET('/', 'Admin', 'NewsController:index');
        $handler->GET('/create', 'Admin', 'NewsController:create');
        $handler->GET('/edit/:id', 'Admin', 'NewsController:edit');
        $handler->POST('/edit/:id', 'Admin', 'NewsController:edit');
        $handler->GET('/delete/:id', 'Admin', 'NewsController:delete');
        $handler->GET('/:id', 'Admin', 'NewsController:get_by_id');
        $handler->POST('/create', 'Admin', 'NewsController:create');
    }
}