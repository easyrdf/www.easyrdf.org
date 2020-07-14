<?php
//
// Script to build packages.json
//

$root = realpath(__DIR__ . "/..");
require "$root/vendor/autoload.php";


$dir = "$root/public/downloads";
$dh = opendir($dir);
if (!$dh) {
    die("Failed to open directory: $dir\n");
}

$packages = array();
while (($filename = readdir($dh)) !== false) {
    if (preg_match('/^(easyrdf-lib-.+).tar.gz$/', $filename, $m)) {
        $dirname = $m[1];
        $filepath = "$dir/$filename";
    } else {
        continue;
    }

    // Extract composer.json using PHAR
    $composer = json_decode(
        file_get_contents("phar://$filepath/$dirname/composer.json"),
        true
    );

    // Add distribution information
    $composer['dist'] = array(
        'type' => 'tar',
        'url' => "https://www.easyrdf.org/downloads/$filename"
    );

    // Add release date, based on when root directory, in the tar, was created
    $tar = new PharData($filepath);
    $mtime = $tar[$dirname]->getMTime();
    $composer['time'] = gmdate("Y-m-d h:m:s", $mtime);

    // Remove irrelevant development dependencies
    unset($composer['require-dev']);

    // Add it to the list of packages
    $name = $composer['name'];
    $version = $composer['version'];
    $packages[$name][$version] = $composer;
}
closedir($dh);


// Reverse-sort by version number
foreach($packages as $name => $package) {
    krsort($packages[$name]);
}

// Sort by package name
ksort($packages);

// Write out packages.json
file_put_contents(
    "$root/public/packages.json",
    json_encode(array(
        'packages' => $packages,
        'notify_batch' => 'https://packagist.org/downloads/'
    ))
);
