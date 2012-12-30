<?php

require '../vendor/autoload.php';

EasyRdf_Namespace::set('easyrdf', 'http://www.easyrdf.org/ns#');

// Load information about the bundled version of EasyRdf
$composer = json_decode(
    file_get_contents('../vendor/njh/easyrdf/composer.json'),
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

// Add support for the markdown tag
$twig = $app->view()->getEnvironment();
$twig->addTokenParser(new \Aptoma\Twig\TokenParser\MarkdownTokenParser());

// Pass the root URL to the view
$app->view()->setData(array(
  'version' => $composer['version'],
  'rootUrl' => $app->request()->getUrl() . $app->request()->getScriptName(),
));

\Slim\Route::setDefaultConditions(array(
    'filename' => '^[\w\.\-]+$'
));

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
    $version = $app->view()->getData('version');
    $app->response()->redirect(
        "https://github.com/njh/easyrdf/blob/$version/examples/$filename",
        302
    );
});

$app->get('/downloads', function () use ($app) {
    $version = $app->view()->getData('version');
    $downloads = array();
    if ($dh = opendir('downloads')) {
        while (($filename = readdir($dh)) !== false) {
            if (preg_match('/^(.+)\-([^\-]+)\.([a-z\.]+)$/', $filename, $m)) {
                $version = $m[2];
                $downloads[$version] = $filename;
            }
        }
        closedir($dh);

        // Sort by version number
        krsort($downloads);
    } else {
        throw new Exception("Failed to open downloads directory");
    }

    $app->view()->setData('downloads', $downloads);
    $app->render('downloads.html');
});

$app->notFound(function () use ($app) {
    $app->render('404.html');
});

// Run app
$app->run();
