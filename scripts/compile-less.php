<?php

$root = realpath(__DIR__ . "/..");
require "$root/vendor/autoload.php";

$less = new lessc;
$less->setImportDir(array(
    "$root/less",
    "$root/vendor/twitter/bootstrap/twitter/bootstrap/less"
));

$less->compileFile(
    "$root/less/easyrdf.less",
    "$root/public/css/bootstrap.css"
);
