<?php
namespace App;

use App\InvestedInterface;

class Container
{
    private $container = [];

    public function push (string $name, InvestedInterface $invested)
    {
        if ($this->has($name)) return;

        $this->container[$name] = $invested;
    }

    public function get (string $name)
    {
        return $this->container[$name] ?? null;
    }

    public function remove (string $name)
    {
        // @TODO
    }

    private function has (string $name): bool
    {
        if (!isset($this->container[$name]) || empty($this->container[$name]))
            return false;
        return true;
    }
}