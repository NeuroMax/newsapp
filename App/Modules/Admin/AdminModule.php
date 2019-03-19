<?php
namespace App\Modules\Admin;


use App\Container;
use App\Module;
use App\Services\Router\Router;

/**
 * Class AdminModule Модуль админки
 * @package App\Modules\Admin
 */
class AdminModule extends Module
{
    /** @var Container */
    private $container;

    /**
     * AdminModule constructor.
     * @param Container $container
     */
    function __construct(Container $container)
    {
        parent::__construct('Admin');
        $this->container = $container;
    }

    /**
     * @return mixed|void
     */
    protected function registerRoutes ()
    {
        /** @var Router $router */
        $router = $this->container->get('Router');

        (new AdminRoutes($router))->register();
    }

    /**
     * @return mixed|void
     */
    public function load()
    {
        $this->registerRoutes();
    }
}