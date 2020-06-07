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
    
    public function rootDir()
    {
        return realpath(__DIR__ . '/../');
    }
    
    public function publicDir()
    {
        return $this->rootDir() . "/public";
    }
}
