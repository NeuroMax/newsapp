<?php
namespace App\Modules\Site;

use App\Services\Router\RouteFabric;
use App\Services\Router\Router;

/**
 * Тут определяем маршруты
 * Class SiteRoutes
 * @package App\Modules\Site
 */
class SiteRoutes
{
    /** @var Router */
    private $router;

    /**
     * SiteRoutes constructor.
     * @param Router $router
     */
    function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     *
     */
    public function register ()
    {
        $handler = new RouteFabric($this->router);
        $handler->GET('/404', 'Site', 'SiteController:notFoundPage');
        $handler->GET('/500', 'Site', 'SiteController:errorServer');

        $handler->GET('/', 'Site', 'NewsController:index');
        $handler->GET('/:id', 'Site', 'NewsController:get_by_id');

    }
}