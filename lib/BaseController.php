<?php

class BaseController
{
    protected $app;
    protected $request;
    protected $response;

    public function __construct(Slim\Slim $app)
    {
        $this->app = $app;
        $this->request = $app->request();
        $this->response = $app->response();
        $this->view = $app->view();
    }
}
