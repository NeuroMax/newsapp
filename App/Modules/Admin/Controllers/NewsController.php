<?php
namespace App\Modules\Admin\Controllers;

use App\Controller;
use App\Entities\News;
use App\Services\Router\Request;
use JasonGrimes\Paginator;

/**
 * Class NewsController Контроллер новостей
 * @package App\Modules\Admin\Controllers
 */
class NewsController extends Controller
{
    /**
     * NewsController constructor.
     * @throws \Exception
     */
    function __construct()
    {
        parent::__construct('Admin');
    }

    /**
     * Action список новостей
     * @param Request $request
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index (Request $request)
    {

        $itemsPerPage = 10;
        $currentPage = $request->getQuery()['p'] ?? 1 ;
        $offset = $currentPage === 1 ? 0 : ($currentPage - 1) * $itemsPerPage;

        $news = new News();
        $news_list = $news::find($itemsPerPage, $offset);
        $countChars = 600;
        $totalItems = $news_list['count'];

        if ($news_list)
        {
            $news_list = array_map(function ($n) use ($countChars) {
                if (mb_strlen($n->text) > $countChars )
                    $n->text = mb_strimwidth($n->text, 0, $countChars) . '...';
                return $n;
            }, $news_list['data']);
        }


        $urlPattern = '/admin?p=(:num)';
        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

        $this->render('news_list.twig', ['title' => 'Список новостей', 'data' => $news_list, 'p' => $paginator]);
    }

    /**
     * Action отдельной новости по id
     * @param Request $request
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function get_by_id (Request $request)
    {
        $id = $request->getParams() ? $request->getParams()['id'] : false;
        $news = new News();
        $news = $news::findID($id);
        if (!$news) $this->redirect('404');
        $this->render('news.twig', ['title' => $news->title, 'data' => $news]);
    }

    /**
     * Action Редактирование новости
     * @param Request $request
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function edit (Request $request)
    {
        $id = $request->getParams() ? $request->getParams()['id'] : false;
        if (!$id) $this->redirect('admin');

        /** @var News $news */
        $news = new News();
        $news = $news::findID($id);
        if (!$news) $this->redirect('404');

        if ($request->getMethod() === Request::METHOD_POST)
        {
            $body = $request->getBody();
            if (isset($body['title']) && !empty($body['title'])) $news->title = $body['title'];
            if (isset($body['text']) && !empty($body['text'])) $news->text = $body['text'];

            if ($news->Save()) $this->redirect("admin/$id");
        } else {
            $this->render('news_form.twig', ['title' => $news->title, 'data' => $news, 'edit' => true]);
        }
    }

    /**
     * Action Удаление новости
     * @param Request $request
     * @throws \ReflectionException
     */
    public function delete (Request $request)
    {
        $id = $request->getParams() ? $request->getParams()['id'] : false;
        if (!$id) $this->redirect('admin');

        /** @var News $news */
        $news = new News();
        $news = $news::findID($id);
        if (!$news) $this->redirect('404');

        $news->Remove();
        $this->redirect('admin');
    }

    /**
     * Action Создание новости
     * @param Request $request
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function create (Request $request)
    {
        if ($request->getMethod() === Request::METHOD_POST)
        {
            $error = null;
            $data = $request->getBody();
            $news = new News();
            $news->title = $data['title'] ?? '';
            $news->text = $data['text'] ?? '';
            if ($id = $news->Create())
                $this->redirect("admin/$id");
        } else $this->render('news_form.twig', ['title' => 'Создание новости']);
    }
}