<?php
//
// Script to convert Markdown documentation to HTML
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

while (($filename = readdir($dh)) !== false) {
    if (preg_match('/^(\d+)-(.+?)\.(md)$/', $filename, $m)) {
        list(,$index,$name,$ext) = $m;
        $inputPath = "$inputDir/$filename";
        $outputPath = "$outputDir/$index-$name.html";
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

    file_put_contents($outputPath, $html);
}
closedir($dh);
