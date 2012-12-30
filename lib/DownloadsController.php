<?php

class DownloadsController extends BaseController
{

    public function indexAction()
    {
        $version = $this->view->getData('version');
        $downloads = array();
        if ($dh = opendir('downloads')) {
            while (($filename = readdir($dh)) !== false) {
                if (preg_match('/^(.+)\-([^\-]+)\.([a-z\.]+)$/', $filename, $m)) {
                    $version = $m[2];
                    $downloads[$version] = $filename;
                }
            }
            closedir($dh);

            // Sort by version number
            krsort($downloads);
        } else {
            throw new Exception("Failed to open downloads directory");
        }

        $this->view->setData('downloads', $downloads);
        $this->app->render('downloads.html');
    }

}
