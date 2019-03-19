<?php
namespace App\Modules\Site\Controllers;


use App\Controller;

/**
 * Class SiteController
 * @package App\Modules\Site\Controllers
 */
class SiteController extends Controller
{
    /**
     * SiteController constructor.
     * @throws \Exception
     */
    function __construct()
    {
        parent::__construct('Site');
    }

    /**
     * Action Страница 404
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function notFoundPage ()
    {
        $this->render('404.twig');
    }

    /**
     * Action Страница 500
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function errorServer ()
    {
        $this->render('500.twig');
    }
}