<?php
//
// Script to convert Markdown documentation to HTML
// and create an index of the files as a Turtle document
//

$root = realpath(__DIR__ . "/..");
require "$root/vendor/autoload.php";

$inputDir = "$root/vendor/easyrdf/easyrdf/docs";
$outputDir = "$root/public/docs";

$Parsedown = new Parsedown();

$dh = opendir($inputDir);
if (!$dh) {
    die("Failed to open directory: $inputDir\n");
}

if (!is_dir($outputDir)) {
    mkdir($outputDir);
}

$docs = new \EasyRdf\Graph();
while (($filename = readdir($dh)) !== false) {
    if (preg_match('/^(\d+)-(.+?)\.(md)$/', $filename, $m)) {
        list(,$index,$name,$ext) = $m;
        $inputPath = "$inputDir/$filename";
        $outputPath = "$outputDir/$index-$name.html";
        $docUrl = "https://www.easyrdf.org/docs/$name";
    } else {
        continue;
    }

    echo "  $filename\n";
    $markdown = file_get_contents($inputPath);
    $html = $Parsedown->text($markdown);

    // FIXME: find better way to pretty print code?
    $html = preg_replace(
      '/<code class="language-(\w+)">/i',
      '<code class="language-${1} prettyprint">',
      $html
    );

    // Now Extract the document title from the HTML
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $title = $doc->getElementsByTagName('h1')[0]->nodeValue;

    $doc = $docs->resource($docUrl, 'foaf:Document');
    $doc->set('foaf:name', $title);
    $doc->set('foaf:index', (int)$index);  # FIXME: find proper property
    $doc->set('foaf:localFile', $outputPath);  # FIXME: find proper property
    $doc->set('foaf:source', $inputPath);  # FIXME: find proper property

    file_put_contents($outputPath, $html);
}
closedir($dh);



// Write to Turtle file
file_put_contents(
    "$root/data/documentation.ttl",
    $docs->serialise('turtle')
);
