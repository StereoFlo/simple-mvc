<?php

namespace App\Controllers;

use App\Models\TestModel;
use Core\Template;
use Core\Request\Request;
use Core\Response\Response;

/**
 * Class IndexController
 * @package Controllers
 */
class IndexController
{
    /**
     * @var Request
     */
    private $req;

    /**
     * IndexController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->req = $request;
    }

    /**
     * @param Request  $request
     * @param Template $template
     *
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request, Template $template, TestModel $testModel): Response
    {
        $res = $testModel->getMedia();
        $test = $request->query()->get('test', 'null');
        return Response::create($template->render('index', ['test' => $test]));
    }

    /**
     * @param string $test1
     * @param string $test2
     * @param string $test3
     */
    public function test(string $test1, string $test2, string $test3)
    {
        var_dump(func_get_args());
    }
}