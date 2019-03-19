<?php
namespace App\Services\Router;

use App\Container;
use App\InvestedInterface;
use App\Module;
use App\ModuleManager;
use App\Service;

class Router extends Service implements InvestedInterface
{
    /** @var Container */
    private $container;
    /** @var array  */
    private $routes = [];
    /** @var Request */
    private $request;
    /** @var string  */
    private $url = '';

    function __construct(Container $container)
    {
        $this->container = $container;
        $this->setUrl();
    }

    public function start (): void
    {
        if (!$route = $this->determineMatchingRoute())
        {
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/404";
            header("Location: $url");
            return;
        }

        /** @var ModuleManager $moduleManager */
        $moduleManager = $this->container->get('ModuleManager');
        /** @var Module $module */
        $module = $moduleManager->get($route->getModule());
        if (!$module) throw new \Exception('Not found module', 404);

        $module->dispatch($route);
    }

    public function registerRoute (Route $route): void
    {
        if (!$this->has($route))
            $this->routes[] = $route;
    }

    private function has (Route &$route): bool
    {
        /** @var Route $r */
        foreach ($this->routes as $r)
        {
            if ($route->getPath() === $r->getPath() && $route->getMethod() === $r->getMethod()) return true;
        }
        return false;
    }

    private function determineMatchingRoute ()
    {
        foreach ($this->routes as $route)
        {
            if($_SERVER['REQUEST_METHOD'] != $route->getMethod()) continue;

            $urlSections = array_values(array_filter(explode('/', $this->url)));
            if (count($route->getMask()) != count($urlSections)) continue;
            if (empty($route->getMask()) && empty($urlSections)) return $route;

            $res = true;
            for ($i = 0; $i < count($route->getMask()); $i++)
            {
                if ($route->getMask()[$i] != '?' && $route->getMask()[$i] != $urlSections[$i]) $res = false;
            }

            if ($res) return $route;
        }
        return null;
    }

    private function setUrl ()
    {
        $this->url = parse_url($_SERVER['REQUEST_URI'])['path'];
    }
}