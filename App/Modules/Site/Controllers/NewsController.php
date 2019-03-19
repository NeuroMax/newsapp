<?php
namespace App\Modules\Site\Controllers;


use App\Controller;
use App\Entities\News;
use App\Services\Router\Request;
use JasonGrimes\Paginator;

/**
 * Class NewsController Контроллер новостей
 * @package App\Modules\Site\Controllers
 */
class NewsController extends Controller
{
    /**
     * NewsController constructor.
     * @throws \Exception
     */
    function __construct()
    {
        parent::__construct('Site');
    }

    /**
     * Action Список новостей
     * @param Request $request
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index (Request $request)
    {
        $itemsPerPage = 5;
        $currentPage = $request->getQuery()['p'] ?? 1 ;
        $offset = $currentPage === 1 ? 0 : ($currentPage - 1) * $itemsPerPage;

        $news = new News();
        $news_list = $news::find($itemsPerPage, $offset);
        $countChars = 500;
        $totalItems = $news_list['count'];

        if ($news_list)
        {
            $news_list = array_map(function ($n) use ($countChars) {
                if (mb_strlen($n->text) > $countChars )
                {
                    $text = strip_tags($n->text);
                    $text = str_replace("&nbsp;",' ',$text);
                    $text = str_replace("&mdash;",'-',$text);
                    $text = str_replace("&laquo;",'"',$text);
                    $text = str_replace("&raquo;",'"',$text);
                    $n->text = mb_strimwidth($text, 0, $countChars) . '...';
                }

                return $n;
            }, $news_list['data']);
        }

        $urlPattern = '/?p=(:num)';
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
        $id = $request->getParams()['id'];

        $news = new News();
        $news = $news::findID($id);

        $this->render('news.twig', ['title' => $news->title, 'data' => $news]);
    }
}