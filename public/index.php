<?php

require '../vendor/autoload.php';

EasyRdf_Namespace::set('easyrdf', 'http://www.easyrdf.org/ns#');

// FIXME: make this nicer
$composer = json_decode(file_get_contents('../vendor/njh/easyrdf/composer.json'), true);
$version = $composer['version'];

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
    $examples = new EasyRdf_Graph();
    $examples->parseFile('../data/examples.ttl', 'turtle');
    $app->view()->setData('examples', $examples->allOfType('easyrdf:Example'));
    $app->render('examples.html');
});

$app->get('/examples/:filename', function ($filename) use ($app) {
    global $version;
    $app->response()->redirect("https://github.com/njh/easyrdf/blob/$version/examples/$filename", 302);
});

$app->get('/downloads', function () use ($app) {
    $app->response()->redirect('http://github.com/njh/easyrdf/downloads', 302);
});

$app->notFound(function () use ($app) {
    $app->render('404.html');
});

// Run app
$app->run();
