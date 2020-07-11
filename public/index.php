<?php

define('ROOT_DIR', realpath(__DIR__ . '/../'));

if (php_sapi_name() == 'cli-server') {
    if (is_file(ROOT_DIR . '/public' . $_SERVER['REQUEST_URI'])) {
        // Get the PHP web server to serve the file
        return false;
    } else {
        // Hackery required to get Slim to behave
        $_SERVER['PATH_INFO'] = $_SERVER['REQUEST_URI'];
        $_SERVER['SCRIPT_NAME'] = '/';
    }
}

require ROOT_DIR . '/vendor/autoload.php';
foreach( glob(ROOT_DIR . '/lib/*.php') as $file ) {
    require $file;
}

\EasyRdf\RdfNamespace::set('easyrdf', 'http://www.easyrdf.org/ns#');

// Load information about the bundled version of EasyRdf
$composer = json_decode(
    file_get_contents(ROOT_DIR . '/vendor/easyrdf/easyrdf/composer.json'),
    true
);

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => ROOT_DIR . '/templates'
));

// Prepare view renderer
\Slim\Extras\Views\Twig::$twigOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath(ROOT_DIR . '/tmp/twig'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view(new \Slim\Extras\Views\Twig());

// Add extensions to Twig
$twig = $app->view()->getEnvironment();
$twig->addExtension(new Twig_Extension_HTMLHelpers());

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

$app->map('/converter', function () use ($app) {
    $controller = new ConverterController($app);
    $controller->convertAction();
})->via('GET', 'POST');

$app->get('/docs', function () use ($app) {
    $controller = new DocumentationController($app);
    $controller->indexAction();
});

$app->get('/docs/EasyRdf/:page', function ($page) use ($app) {
    $app->redirect("/docs/api/$page", 301);
});

$app->get('/docs/api', function () use ($app) {
    $app->redirect("/docs/api/classes.html", 301);
});
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
    $examples = new \EasyRdf\Graph();
    $examples->parseFile(ROOT_DIR . '/data/examples.ttl', 'turtle');
    $app->view()->setData('examples', $examples->allOfType('easyrdf:Example'));
    $app->render('examples.html');
});

$app->get('/examples/:filename', function ($filename) use ($app) {
    $version = $app->view()->getData('version');
    $app->redirect(
        "https://github.com/easyrdf/easyrdf/blob/$version/examples/$filename",
        302
    );
});

$app->get('/support', function () use ($app) {
    $app->render('support.html');
});

// Remove trailing slashes
// FIXME: this doesn't work properly
// $app->get('/:path+/', function ($path) use ($app) {
//     $rootUrl = $app->view()->getData('rootUrl');
//     $app->redirect(
//         $rootUrl . '/' . implode('/', array_splice($path, 0, -1)),
//         301
//     );
// });

$app->notFound(function () use ($app) {
    $app->render('404.html');
});

// Run app
$app->run();
