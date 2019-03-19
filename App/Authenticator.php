<?php

namespace App;

use App\Entities\User;
use App\Services\Config;
use Firebase\JWT\JWT;

/**
 * Class Authenticator
 * @package App
 */
class Authenticator
{

    /**
     * Валидация токена
     * @param string $token
     * @return object
     * @throws \Exception
     */
    public static function validate (string $token)
    {
        /** @var Config $conf */
        $conf = new Config();
        $secret = $conf->get('secrets:token');

        return JWT::decode($token, $secret, array('HS256'));
    }

    /**
     * Генерация токена
     * @param User $user
     * @return string
     * @throws \Exception
     */
    public static function authenticate (User $user)
    {
        /** @var Config $conf */
        $conf = new Config();
        $secret = $conf->get('secrets:token');

        return JWT::encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ], $secret, 'HS256');
    }

    /**
     * Шифрование пароля
     * @param string $password
     * @return bool|string
     */
    public static function decodePassword (string $password)
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 11]);
    }

    /**
     * Валидация пароля
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function passwordVerify (string $password, string $hash)
    {
        return password_verify($password, $hash);
    }
}