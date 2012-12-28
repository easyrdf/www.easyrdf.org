<?php

require '../vendor/autoload.php';

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => '../templates'
));

// Prepare view renderer
\Slim\Extras\Views\Twig::$twigOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../tmp/twig'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view(new \Slim\Extras\Views\Twig());

// Add support for the markdown tag
$twig = $app->view()->getEnvironment();
$twig->addTokenParser(new \Aptoma\Twig\TokenParser\MarkdownTokenParser());

// Pass the root URL to the view
$app->view()->setData(
  'rootUrl',
  $app->request()->getUrl() . $app->request()->getScriptName()
);

// Define routes
$app->get('/', function () use ($app) {
    $app->render('home.html');
});

$app->get('/docs', function () use ($app) {
    $app->response()->redirect('/docs/api', 302);
});

$app->get('/converter', function () use ($app) {
    $app->response()->redirect('http://converter.easyrdf.org/', 302);
});

$app->get('/examples', function () use ($app) {
    $app->response()->redirect('http://github.com/njh/easyrdf/tree/master/examples', 302);
});

$app->get('/downloads', function () use ($app) {
    $app->response()->redirect('http://github.com/njh/easyrdf/downloads', 302);
});

$app->notFound(function () use ($app) {
    $app->render('404.html');
});

// Run app
$app->run();
