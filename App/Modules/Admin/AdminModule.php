<?php
namespace App\Modules\Admin;


use App\Container;
use App\Module;
use App\Services\Router\Router;

class AdminModule extends Module
{
    /** @var Container */
    private $container;

    function __construct(Container $container)
    {
        parent::__construct('Admin');
        $this->container = $container;
    }

    protected function registerRoutes ()
    {
        /** @var Router $router */
        $router = $this->container->get('Router');

        (new AdminRoutes($router))->register();
    }

    public function load()
    {
        $this->registerRoutes();
    }
}