<?php

class DocumentationController extends BaseController
{

    public function indexAction()
    {
        $docs = $this->getDocumentation();
        $this->view->setData('docs', $docs);
        $this->view->setData('classes', $this->getClasses());
        $this->app->render('documentation-index.html');
    }

    public function showAction($name)
    {
        $docs = $this->getDocumentation();
        if (array_key_exists($name, $docs)) {
            $doc = $docs[$name];
            $this->view->setData('doc', $doc);
            $this->view->setData('docs', $docs);
            $this->view->setData(
                'text',
                file_get_contents($doc->get('foaf:localFile'))
            );
            $this->app->render('documentation-show.html');
        } else {
            $this->app->notFound();
        }
    }

    protected function getDocumentation()
    {
        $docs = new \EasyRdf\Graph();
        $docs->parseFile(ROOT_DIR . '/data/documentation.ttl', 'turtle');

        // FIXME: is there a built-in PHP function for doing this?
        $assocArray = array();
        foreach($docs->allOfType('foaf:Document') as $doc) {
           $assocArray[$doc->localName()] = $doc;
        }

        return $assocArray;
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
