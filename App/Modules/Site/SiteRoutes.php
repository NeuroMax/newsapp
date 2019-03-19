<?php
namespace App\Modules\Site;

use App\Services\Router\RouteHandler;
use App\Services\Router\Router;

class SiteRoutes
{
    /** @var Router */
    private $router;

    function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function register ()
    {
        /** @var RouteHandler $handler */
        $handler = new RouteHandler($this->router);
        $handler->GET('/404', 'Site', 'SiteController:notFoundPage');
        $handler->GET('/500', 'Site', 'SiteController:errorServer');

        $handler->GET('/', 'Site', 'NewsController:index');
        $handler->GET('/:id', 'Site', 'NewsController:get_by_id');

    }
}