<?php
namespace App\Modules\Site;


use App\Container;
use App\Module;
use App\Services\Router\Router;

class SiteModule extends Module
{
    /** @var Container */
    private $container;

    function __construct(Container $container)
    {
        parent::__construct('Site');
        $this->container = $container;
    }

    protected function registerRoutes()
    {
        /** @var Router $router */
        $router = $this->container->get('Router');

        (new SiteRoutes($router))->register();
    }

    public function load()
    {
        $this->registerRoutes();
    }
}