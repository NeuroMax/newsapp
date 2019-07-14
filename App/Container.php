<?php
namespace App;

use App\InvestedInterface;

/**
 * Контейнер для сервисов
 * Class Container
 * @package App
 */
class Container
{
    /**
     * @var array
     */
    private $container = [];

    /**
     * Добавление в массив
     * @param string $name Тмя сервиса
     * @param \App\InvestedInterface $invested
     */
    public function push (string $name, InvestedInterface $invested)
    {
        if ($this->has($name)) return;

        $this->container[$name] = $invested;
    }

    /**
     * Получение из массива
     * @param string $name Имя сервиса
     * @return mixed|null
     */
    public function get (string $name)
    {
        return $this->container[$name] ?? null;
    }

    /**
     * Удаление из массива
     * @param string $name Имя сервиса
     */
    public function remove (string $name)
    {
        // @TODO lets do it
    }

    /**
     * Проверка на наличие в массиве
     * @param string $name Имя сервиса
     * @return bool
     */
    private function has (string $name): bool
    {
        if (!isset($this->container[$name]) || empty($this->container[$name]))
            return false;
        return true;
    }
}