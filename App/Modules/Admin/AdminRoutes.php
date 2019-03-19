<?php
namespace App\Modules\Admin;

use App\Services\Router\RouteFabric;
use App\Services\Router\Router;

/**
 * Class AdminRoutes Тут определяем маршруты
 * @package App\Modules\Admin
 */
class AdminRoutes
{
    /** @var Router */
    private $router;

    /**
     * AdminRoutes constructor.
     * @param Router $router
     */
    function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Регистрация маршрутов
     */
    public function register ()
    {
        $handler = new RouteFabric($this->router, '/admin');

        $handler->POST('/user', 'Admin', 'UserController:create');
        $handler->GET('/signIn', 'Admin', 'UserController:signIn');
        $handler->GET('/signOut', 'Admin', 'UserController:signOut', ['auth' => true]);
        $handler->POST('/signIn', 'Admin', 'UserController:signIn');

        $handler->GET('/', 'Admin', 'NewsController:index', ['auth' => true]);
        $handler->GET('/create', 'Admin', 'NewsController:create', ['auth' => true]);
        $handler->GET('/edit/:id', 'Admin', 'NewsController:edit', ['auth' => true]);
        $handler->POST('/edit/:id', 'Admin', 'NewsController:edit', ['auth' => true]);
        $handler->GET('/delete/:id', 'Admin', 'NewsController:delete', ['auth' => true]);
        $handler->GET('/:id', 'Admin', 'NewsController:get_by_id', ['auth' => true]);
        $handler->POST('/create', 'Admin', 'NewsController:create', ['auth' => true]);
    }
}