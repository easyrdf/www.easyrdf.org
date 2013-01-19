<?php

class DownloadsController extends BaseController
{
    public function indexAction()
    {
        $this->view->setData(
            'downloads',
            $this->getDownloads()
        );
        $this->app->render('downloads.html');
    }

    protected function getDownloads()
    {
        $downloads = array();
        if ($dh = opendir('downloads')) {
            while (($filename = readdir($dh)) !== false) {
                if (preg_match('/^(.+?)\-([^\-]+?)\.([a-z\.]+)$/', $filename, $m)) {
                    list(,$name, $version, $type) = $m;

                    // Get release date
                    $mtime = filemtime("downloads/$filename");

                    $downloads[] = array(
                        'name' => $name,
                        'filename' => $filename,
                        'url' => "http://www.easyrdf.org/downloads/$filename",
                        'version' => $version,
                        'type' => $type,
                        'releaseDate' => gmdate('Y-m-d', $mtime),
                        'size' => $this->humanFilesize("downloads/$filename", 1)
                     );
                }
            }
            closedir($dh);
        }

        // Sort by version number
        usort($downloads, function ($a, $b) {
            return version_compare($b['version'], $a['version']);
        });

        return $downloads;
    }

    protected function humanFilesize($filename, $decimals = 2) {
        $sz = 'BKMGTP';
        $bytes = filesize($filename);
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

}
