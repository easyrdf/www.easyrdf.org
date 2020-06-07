<?php

class DownloadsController extends BaseController
{
    public function indexAction()
    {
        $this->view->setData(
            'downloads',
            $this->getDownloads(8)
        );
        $this->app->render('downloads.html');
    }

    protected function getDownloads($limit=NULL)
    {
        $downloads = array();
        if ($dh = opendir($this->publicDir() . '/downloads')) {
            while (($filename = readdir($dh)) !== false) {
                if (preg_match('/^(.+?)\-([^\-]+?)\.([a-z\.]+)$/', $filename, $m)) {
                    list(,$name, $version, $type) = $m;

                    // Get release date
                    $filepath = $this->publicDir() . "/downloads/$filename";
                    $mtime = filemtime($filepath);

                    $downloads[] = array(
                        'name' => $name,
                        'filename' => $filename,
                        'url' => "http://www.easyrdf.org/downloads/$filename",
                        'version' => $version,
                        'type' => $type,
                        'releaseDate' => gmdate('Y-m-d', $mtime),
                        'size' => $this->humanFilesize($filepath, 1)
                     );
                }
            }
            closedir($dh);
        }

        // Sort by version number
        usort($downloads, function ($a, $b) {
            return version_compare($b['version'], $a['version']);
        });

        return array_slice($downloads, 0, $limit);
    }

    protected function humanFilesize($filename, $decimals = 2) {
        $sz = 'BKMGTP';
        $bytes = filesize($filename);
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

}
