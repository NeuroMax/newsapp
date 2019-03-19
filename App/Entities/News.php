<?php


namespace App\Entities;


use App\Services\ORM;

class News extends ORM
{
    public $id;
    public $title;
    public $text;
    public $createdAt;
}
