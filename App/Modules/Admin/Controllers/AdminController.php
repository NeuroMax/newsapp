<?php
namespace App\Modules\Admin\Controllers;

use App\Controller;

/**
 * Class AdminController
 * @package App\Modules\Admin\Controllers
 */
class AdminController extends Controller
{
    /**
     * AdminController constructor.
     * @throws \Exception
     */
    function __construct()
    {
        parent::__construct('Admin');
    }

    /**
     *  Action страница 404
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function not_found_action ()
    {
        $this->render('404.twig');
    }
}
