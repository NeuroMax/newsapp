<?php
namespace App\Modules\Site;


use App\Container;
use App\Module;
use App\Services\Router\Router;

/**
 * Модуль основного сайта
 * Class SiteModule
 * @package App\Modules\Site
 */
class SiteModule extends Module
{
    /** @var Container */
    private $container;

    /**
     * SiteModule constructor.
     * @param Container $container
     */
    function __construct(Container $container)
    {
        parent::__construct('Site');
        $this->container = $container;
    }

    /**
     * @return mixed|void
     */
    protected function registerRoutes()
    {
        /** @var Router $router */
        $router = $this->container->get('Router');

        (new SiteRoutes($router))->register();
    }

    /**
     * @return mixed|void
     */
    public function load()
    {
        $this->registerRoutes();
    }
}