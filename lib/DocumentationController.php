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
                file_get_contents("docs/".$info['filename'])
            );
            $this->app->render('documentation-show.html');
        } else {
            $this->app->notFound();
        }
    }

    protected function getDocumentation()
    {
        $docs = array();
        if ($dh = opendir('docs')) {
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
        if ($dh = opendir('docs/api')) {
            while (($filename = readdir($dh)) !== false) {
                if (preg_match('/^(EasyRdf_\w+)/', $filename, $m)) {
                    $classes[] = array(
                        'name' => $m[1],
                        'path' => "/docs/api/$filename",
                        'depth' => substr_count($m[1], '_')
                    );
                }
            }
            closedir($dh);
        }

        // Sort by name
        sort($classes);

        return $classes;
    }
}
