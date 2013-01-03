<?php
//
// Script to build API documentation
//

$root = realpath(__DIR__ . "/..");
require "$root/vendor/autoload.php";

use Sami\Sami;
use Sami\Message;

$errorCount = 0;

function messageCallback($message, $data)
{
    global $errorCount;

    switch ($message) {
        case Message::PARSE_CLASS:
            list($progress, $class) = $data;
            print "Parsing class: $class ($progress%)\n";
            break;
        case Message::PARSE_ERROR:
            foreach ($data as $error) {
                file_put_contents('php://stderr', "$error\n");
                $errorCount++;
            }
            break;
        case Message::RENDER_PROGRESS:
            list ($section, $message, $progress) = $data;
            print "Rendering $section: $message ($progress%)\n";
            break;
        case Message::SWITCH_VERSION:
        case Message::PARSE_VERSION_FINISHED:
        case Message::RENDER_VERSION_FINISHED:
            // Ignore
            break;
    }
}


$iterator = Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in("$root/vendor/easyrdf/easyrdf/lib")
;

$sami = new Sami($iterator, array(
    'title'               => 'EasyRdf API Documentation',
    'versions'            => '0.7.1',
    'theme'               => 'enhanced',
    'build_dir'           => "$root/public/docs/api/",
    'cache_dir'           => "$root/tmp/sami",
    'favicon'             => '/favicon.ico',
    'include_parent_data' => true,
    'simulate_namespaces' => true,
    'default_opened_level' => 1,
));

$sami['project']->update('messageCallback', $force=true);

exit($errorCount);
