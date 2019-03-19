<?php
namespace App\Modules\Site\Controllers;


use App\Controller;
use App\Entities\News;
use App\Services\Router\Request;
use JasonGrimes\Paginator;

class NewsController extends Controller
{
    function __construct()
    {
        parent::__construct('Site');
    }

    public function index (Request $request)
    {
        $itemsPerPage = 5;
        $currentPage = $request->getQuery()['p'] ?? 1 ;
        $offset = $currentPage === 1 ? 0 : ($currentPage - 1) * $itemsPerPage;

        $news = new News();
        $news_list = $news::find($itemsPerPage, $offset);
        $countChars = 200;
        $totalItems = $news_list['count'];

        $news_list = array_map(function ($n) use ($countChars) {
            if (mb_strlen($n->text) > $countChars )
                $n->text = mb_strimwidth($n->text, 0, $countChars) . '...';
            return $n;
        }, $news_list['data']);


        $urlPattern = '/?p=(:num)';
        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

        $this->render('news_list.twig', ['title' => 'Список новостей', 'data' => $news_list, 'p' => $paginator]);
    }

    public function get_by_id (Request $request)
    {
        $id = $request->getParams()['id'];

        $news = new News();
        $news = $news::findID($id);

        $this->render('news.twig', ['title' => $news->title, 'data' => $news]);
    }

    public function create (Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getBody();
            $news = new News();
            $news->title = $data['title'] ?? '';
            $news->text = $data['text'] ?? '';
            $this->render('news.twig', array_merge(['title' => 'Index page'], ['data' => $news->_toArray()]));
        }

        $this->render('news_form.twig', ['title' => 'Index page']);
    }
}