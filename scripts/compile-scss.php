<?php
//
// Script to build custom Bootstrap CSS from SASS sources
//

$root = realpath(__DIR__ . "/..");
require "$root/vendor/autoload.php";

use ScssPhp\ScssPhp\Compiler;

$scss = new Compiler();
$scss->addImportPath("$root/scss");
$scss->addImportPath("$root/vendor/twbs/");

$custom = file_get_contents("$root/scss/bootstrap-custom.scss");
file_put_contents(
  "$root/public/css/bootstrap.css",
  $scss->compile($custom)
);


// Location of bootstrap source LESS files
// $bootstrap = "$root/vendor/twitter/bootstrap/less";
//
// $less = new lessc;
// $less->setImportDir(array(
//     "$root/less",
//     $bootstrap
// ));
//
// $less->compileFile(
//     "$root/less/bootstrap.less",
//     "$root/public/css/bootstrap.css"
// );
//
// $less->compileFile(
//     "$bootstrap/responsive.less",
//     "$root/public/css/bootstrap-responsive.css"
// );
