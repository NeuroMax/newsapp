<?php
namespace App\Modules\Admin\Controllers;

use App\Controller;

class AdminController extends Controller
{
    function __construct()
    {
        parent::__construct('Admin');
    }

    public function not_found_action ()
    {
        $this->render('404.twig');
    }
}
