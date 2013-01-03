<?php

class DownloadsController extends BaseController
{
    public function indexAction()
    {
        $version = $this->view->getData('version');
        $downloads = array();
        foreach ($this->buildPackageData() as $name => $versions) {
            foreach ($versions as $version => $info) {
                $downloads[] = basename($info['dist']['url']);
            }
        }

        $this->view->setData('downloads', $downloads);
        $this->app->render('downloads.html');
    }

    public function packagesAction()
    {
        $this->response['Content-Type'] = 'application/json';
        $this->response->body(
            json_encode(array('packages' => $this->buildPackageData()))
        );
    }


    protected function buildPackageData()
    {
        $packages = array();
        if ($dh = opendir('downloads')) {
            while (($filename = readdir($dh)) !== false) {
                if (preg_match('/^(.+?)\-([^\-]+?)\.([a-z\.]+)$/', $filename, $m)) {
                    list(,$name, $version, $type) = $m;
                    if (substr($type, 0, 4) === 'tar.') {
                        $type = 'tar';
                    }

                    $packages["easyrdf/$name"][$version] = array(
                        'name' => "easyrdf/$name",
                        'version' => $version,
                        'dist' => array(
                            'url' => "http://www.easyrdf.org/downloads/$filename",
                            'type' => $type
                        )
                    );
                }
            }
            closedir($dh);
        }

        // Reverse-sort by version number
        foreach($packages as $name => $package) {
            krsort($packages[$name]);
        }

        // Sort by package name
        ksort($packages);

        return $packages;
    }

}
