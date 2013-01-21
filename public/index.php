<?php

require '../vendor/autoload.php';
foreach( glob(__DIR__ . '/../lib/*.php') as $file ) {
    require $file;
}

EasyRdf_Namespace::set('easyrdf', 'http://www.easyrdf.org/ns#');

// Load information about the bundled version of EasyRdf
$composer = json_decode(
    file_get_contents('../vendor/easyrdf/easyrdf/composer.json'),
    true
);

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

// Pass the root URL to the view
$app->view()->setData(array(
  'version' => $composer['version'],
  'rootUrl' => $app->request()->getUrl() . $app->request()->getScriptName(),
));

\Slim\Route::setDefaultConditions(array(
    'filename' => '[\w\.\-]+'
));


// Define routes
$app->get('/', function () use ($app) {
    $app->render('home.html');
});

$app->get('/converter', function () use ($app) {
    $app->redirect('http://converter.easyrdf.org/', 302);
});

$app->get('/docs', function () use ($app) {
    $controller = new DocumentationController($app);
    $controller->indexAction();
});

$app->get('/docs/api', function () use ($app) {
    $app->render('api.html');
});

$app->get('/docs/:name', function ($name) use ($app) {
    $controller = new DocumentationController($app);
    $controller->showAction($name);
});

$app->get('/downloads', function () use ($app) {
    $controller = new DownloadsController($app);
    $controller->indexAction();
});

$app->get('/examples', function () use ($app) {
    $examples = new EasyRdf_Graph();
    $examples->parseFile('../data/examples.ttl', 'turtle');
    $app->view()->setData('examples', $examples->allOfType('easyrdf:Example'));
    $app->render('examples.html');
});

$app->get('/examples/:filename', function ($filename) use ($app) {
    $version = $app->view()->getData('version');
    $app->redirect(
        "https://github.com/njh/easyrdf/blob/$version/examples/$filename",
        302
    );
});

$app->get('/support', function () use ($app) {
    $app->render('support.html');
});

$app->notFound(function () use ($app) {
    $app->render('404.html');
});

// Run app
$app->run();
