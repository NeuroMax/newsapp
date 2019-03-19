<?php
namespace App\Modules\Site\Controllers;


use App\Controller;

class SiteController extends Controller
{
    function __construct()
    {
        parent::__construct('Site');
    }

    public function notFoundPage ()
    {
        $this->render('404.twig');
    }

    public function errorServer ()
    {
        $this->render('500.twig');
    }
}