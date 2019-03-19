<?php
namespace App\Entities;

use App\Services\ORM;

class User extends ORM
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $createdAt;
}