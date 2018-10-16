<?php

namespace App\Controllers;

use App\Services\SharedService;
use App\Helpers\ConfigHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Twig_Environment;

abstract class BaseController
{
    /** @var SharedService */
    protected $service;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var Twig_Environment */
    protected $twig;

    /** @var ConfigHelper */
    protected $configHelper;

    /** @var bool */
    private $hasResponse = false;

    public function __construct(
        SharedService $service,
        Request $request,
        Response $response,
        Twig_Environment $twig,
        ConfigHelper $configHelper
    ) {
        $this->service = $service;
        $this->request = $request;
        $this->response = $response;
        $this->twig = $twig;
        $this->configHelper = $configHelper;
    }

    public function render($templatePath = '', array $data = [], $conttentType = 'text/html')
    {
        $this->response->headers->set('content-type', $conttentType);
        $this->response->setContent(
            $this->twig->render(
                $templatePath,
                $data
            )
        );
        $this->hasResponse = true;
    }

    public function redirect($url = '')
    {
        $this->response = new RedirectResponse($url);
        $this->hasResponse = true;
    }

    public function response()
    {
        if ($this->hasResponse === true) {
            return $this->response->send();
        }
    }
}
