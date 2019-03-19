<?php
namespace App;

use App\Module;

class ModuleManager implements InvestedInterface
{

    private $modules = [];

    public function get (string $name): Module
    {
        return $this->modules[$name];
    }

    public function set (string $name, Module $module)
    {
        if ($this->has($name)) return;
        $this->modules[$name] = $module;
    }

    private function has (string $name): bool
    {
        if (!isset($this->modules[$name]) || empty($this->modules[$name]))
            return false;
    }

    public function loadModules ()
    {
        /** @var Module $module */
        foreach ($this->modules as $module)
        {
            $module->load();
        }
    }

}