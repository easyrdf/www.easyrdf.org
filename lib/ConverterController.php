<?php

class ConverterController extends BaseController
{

    public function convertAction()
    {
        $inputFormats = array('guess' => 'Guess');
        $outputFormats = array();
        foreach (EasyRdf_Format::getFormats() as $format) {
            if ($format->getParserClass()) {
                $inputFormats[$format->getName()] = $format->getLabel();
            }
            if ($format->getSerialiserClass()) {
                $outputFormats[$format->getName()] = $format->getLabel();
            }
        }

        $params = $this->request->post();
        if (!isset($params['in']))
            $params['in'] = 'guess';
        if (!isset($params['out']))
            $params['out'] = 'turtle';

        $this->view->appendData($params);
        $this->view->setData('inputFormats', $inputFormats);
        $this->view->setData('outputFormats', $outputFormats);
        $this->app->render('converter.html');
    }

}
