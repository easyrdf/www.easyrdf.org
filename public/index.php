<?php

require '../vendor/autoload.php';

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => '../templates'
));

// Prepare view renderer
\Slim\Extras\Views\Twig::$twigOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
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
    $app->render('home.twig');
});

$app->notFound(function () use ($app) {
    $app->render('404.html');
});

// Run app
$app->run();
