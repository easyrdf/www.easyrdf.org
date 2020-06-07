<?php
//
// Script to build list of examples
//

$root = realpath(__DIR__ . "/..");
require "$root/vendor/autoload.php";

\EasyRdf\RdfNamespace::set('easyrdf', 'http://www.easyrdf.org/ns#');

$dir = "$root/vendor/easyrdf/easyrdf/examples/";
$dh = opendir($dir);
if (!$dh) {
    die("Failed to open directory: $dir\n");
}

$filenames = array();
while (($filename = readdir($dh)) !== false) {
    if (substr($filename, 0, 1) == '.' or
        $filename == 'index.php' or
        $filename == 'html_tag_helpers.php') {
        continue;
    }
    
    $filenames[] = $filename;
}
closedir($dh);

sort($filenames);

$examples = new \EasyRdf\Graph();
foreach($filenames as $filename) {
    $lines = file(
        $dir . DIRECTORY_SEPARATOR . $filename,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    $example = $examples->resource("http://www.easyrdf.org/examples/$filename", 'easyrdf:Example');

    $startDoc = false;
    $tags = array();
    $text = array();
    $para = '';
    foreach ($lines as $line) {
        if (preg_match("/^\s*\/\*\*/", $line, $m)) {
            $startDoc = true;
            $tags = array();
        } else if ($startDoc && preg_match("/^\s+\*\//", $line, $m)) {
            if (!empty($para))
                $text[] = $para;
            break;
        } else if ($startDoc && preg_match("/^\s+\*\s+@(\w+)\s+(.*)/", $line, $m)) {
            $tags[$m[1]] = $m[2];
        } else if ($startDoc && preg_match("/^\s+\*\s*$/", $line, $m)) {
            if (!empty($para))
                $text[] = $para;
            $para = '';
       } else if ($startDoc && preg_match("/^\s+\*\s*(.*)/", $line, $m)) {
            if ($para) $para .= ' ';
            $para .= $m[1];
        }
    }

    $example->set('rdfs:comment', array_shift($text));
    if (!empty($text)) {
        $html = new \EasyRdf\Literal\HTML("<p>".implode("</p><p>",$text)."</p>");
        $example->set('dc:description', $html);
    }
}

// Write to Turtle file
file_put_contents(
    "$root/data/examples.ttl",
    $examples->serialise('turtle')
);
