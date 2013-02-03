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

        // Get the request parameters
        $params = $this->request->params();
        if (!isset($params['in']))
            $params['in'] = 'guess';
        if (!isset($params['out']))
            $params['out'] = 'turtle';

        if (isset($params['uri']) or isset($params['data'])) {
            try {
                // Parse the input
                $graph = new EasyRdf_Graph($params['uri']);
                $count = 0;
                if (empty($params['data'])) {
                    $count = $graph->load($params['uri'], $params['in']);
                } else {
                    $count = $graph->parse($params['data'], $params['in'], $params['uri']);
                }
    
                // Lookup the output format
                $format = EasyRdf_Format::getFormat($params['out']);
                if ($format) {
                    // Serialise to the new output format
                    $output = $graph->serialise($format);
                    if (!is_scalar($output)) {
                        // Convert non-strings
                        $output = var_export($output, true);
                    }

                    // Send the output straight back to the client, when in raw mode
                    if (isset($params['raw'])) {
                        $mime = $format->getDefaultMimeType();
                        if ($mime) {
                            $this->response['Content-Type'] = $mime;
                        }
                        $this->response->write($output, true);
                        return;
                    } else {
                        $this->view->setData('count', $count);
                        $this->view->setData('output', $output);
                    }
                } else {
                    $this->view->setData('error', 'Invalid output format');
                }
            } catch (Exception $e) {
                $this->view->setData('error', $e->getMessage());
            }
        }

        $this->view->appendData($params);
        $this->view->setData('inputFormats', $inputFormats);
        $this->view->setData('outputFormats', $outputFormats);
        $this->app->render('converter.html');
    }

}
