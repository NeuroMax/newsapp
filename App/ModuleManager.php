<?php
namespace App;

use App\Module;

/**
 * Класс менеджер модулей
 * Class ModuleManager
 * @package App
 */
class ModuleManager implements InvestedInterface
{

    /**
     * @var array
     */
    private $modules = [];

    /**
     * @param string $name
     * @return \App\Module
     */
    public function get (string $name): Module
    {
        return $this->modules[$name];
    }

    /**
     * @param string $name
     * @param \App\Module $module
     */
    public function set (string $name, Module $module)
    {
        if ($this->has($name)) return;
        $this->modules[$name] = $module;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function has (string $name): bool
    {
        if (!isset($this->modules[$name]) || empty($this->modules[$name]))
            return false;
    }

    /**
     *
     */
    public function loadModules ()
    {
        /** @var Module $module */
        foreach ($this->modules as $module)
        {
            $module->load();
        }
    }

}