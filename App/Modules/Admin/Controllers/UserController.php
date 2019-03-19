<?php
namespace App\Modules\Admin\Controllers;

use App\Authenticator;
use App\Controller;
use App\Entities\User;
use App\Services\Router\Request;

/**
 * Контроллер пользователей
 * Class UserController
 * @package App\Modules\Admin\Controllers
 */
class UserController extends Controller
{
    /**
     * UserController constructor.
     * @throws \Exception
     */
    function __construct()
    {
        parent::__construct('Admin');
    }

    /**
     * Action Создание пользователя
     * @param Request $request
     * @throws \ReflectionException
     */
    public function create (Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getBody();
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = Authenticator::decodePassword($data['password']);
            $res = $user->Create($err);
            if ($err)
                throw new \Exception($err);
            header('Content-Type: application/json');
            echo json_encode($res);
        }
    }

    /**
     * Action Авторизация пользователя
     * @param Request $request
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function signIn (Request $request)
    {
        if ($request->getMethod() === Request::METHOD_POST)
        {
            $body = $request->getBody() ?? false;
            $email = $body['email'];
            $password = $body['password'];

            /** @var User $user */
            $user = new User();

            if (!$user = $user::find(1, 0, ['email' => $email])['data'][0])
            {
                $this->render('signIn.twig', array_merge(['title' => 'SignIn'], ['error' => 'Логин или пароль не верны']));
                return;
            }

            if (!Authenticator::passwordVerify($password, $user->password))
            {
                $this->render('signIn.twig', array_merge(['title' => 'SignIn'], ['error' => 'Логин или пароль не верны']));
                return;
            } else {
                setcookie('token', Authenticator::authenticate($user));
                $this->redirect('admin');
            }
        } else {
            $this->render('signIn.twig', array_merge(['title' => 'SignIn']));
        }
    }

    /**
     * Action Выход пользователя
     */
    public function signOut ()
    {
        unset($_COOKIE['token']);
        setcookie('token', '', time() - 3600);
        $this->redirect('admin');
    }
}