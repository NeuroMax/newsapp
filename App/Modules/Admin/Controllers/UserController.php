<?php
namespace App\Modules\Admin\Controllers;


use App\Controller;
use App\Entities\User;
use App\Services\Router\Request;

class UserController extends Controller
{
    function __construct()
    {
        parent::__construct('Admin');
    }

    public function index (Request $request)
    {
        $this->render('user_list.twig', ['title' => 'Пользователи']);
    }

    public function get_by_id (Request $request)
    {
        $id = $request->getParams()['id'];

        $this->render('user.twig', ['title' => "Пользователь с id: $id", 'data' => [ 'id' => $id ]]);
    }

    public function create (Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getBody();
            $user = new User();
            $user->set_name($data['name'] ?? '');
            $user->set_email($data['email'] ?? '');
            $user->set_password($data['password'] ?? '');
            $user->Create();

            $this->render('user.twig', array_merge(['title' => 'Index page'], ['data' => $user::_getVars()]));
        }

        $this->render('user_form.twig', ['title' => 'Index page']);
    }
}