<?php

class DocumentationController extends BaseController
{

    public function indexAction()
    {
        $index = $this->getDocumentation();
        $this->view->setData('items', $index);
        $this->view->setData('classes', $this->getClasses());
        $this->app->render('documentation-index.html');
    }

    public function showAction($name)
    {
        $index = $this->getDocumentation();
        if (array_key_exists($name, $index)) {
            $info = $index[$name];
            $this->view->setData('items', $index);
            $this->view->appendData($info);
            $this->view->setData(
                'text',
                file_get_contents($this->publicDir() . '/docs/'.$info['filename'])
            );
            $this->app->render('documentation-show.html');
        } else {
            $this->app->notFound();
        }
    }

    protected function getDocumentation()
    {
        $docs = array();
        if ($dh = opendir($this->publicDir() . '/docs')) {
            while (($filename = readdir($dh)) !== false) {
                if (preg_match('/^(\d+)\-(.+?)\.(\w+)$/', $filename, $m)) {
                    list(,$index, $name, $format) = $m;

                    $docs[$name] = array(
                        'filename' => $filename,
                        'index' => $index,
                        'name' => $name,
                        'title' => ucwords(str_replace('-', ' ', strtolower($name))),
                        'format' => $format
                     );
                }
            }
            closedir($dh);
        }

        // Sort by filename
        uasort($docs, function ($a, $b) {
            return strcmp($a['filename'], $b['filename']);
        });

        return $docs;
    }

    protected function getClasses()
    {
        $classes = array();
        $dir = $this->publicDir() . '/docs/api/EasyRdf/';
        $filenames = glob($dir . '{,*/,*/*/}*.html', GLOB_BRACE);
        foreach($filenames as $filename) {
            $subpath = substr($filename, strlen($dir));
            $name = 'EasyRdf\\' . str_replace('/', '\\', str_replace('.html', '', $subpath));
            $classes[$name] = array(
                'name' => $name,
                'path' => "/docs/api/EasyRdf/$subpath",
                'depth' => substr_count($subpath, '/')
            );
        
        }

        // Sort by name
        ksort($classes);

        return $classes;
    }
}
