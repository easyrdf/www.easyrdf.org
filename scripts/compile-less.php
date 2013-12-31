<?php
//
// Script to build custom Bootstrap CSS from LESS sources
//

$root = realpath(__DIR__ . "/..");
require "$root/vendor/autoload.php";

// Location of bootstrap source LESS files
$bootstrap = "$root/vendor/twitter/bootstrap/less";

$less = new lessc;
$less->setImportDir(array(
    "$root/less",
    $bootstrap
));

$less->compileFile(
    "$root/less/bootstrap.less",
    "$root/public/css/bootstrap.css"
);

$less->compileFile(
    "$bootstrap/responsive.less",
    "$root/public/css/bootstrap-responsive.css"
);
