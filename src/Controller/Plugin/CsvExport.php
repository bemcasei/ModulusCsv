<?php

namespace ModulusCsv\Controller\Plugin;

use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * CsvExport
 *
 * @category ModulusCsv
 * @package ModulusCsv\Controller\Plugin
 * @author BemCasei <contato@bemcasei.com.br>
 */
class CsvExport extends AbstractPlugin
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var array
     */
    protected $header;

    /**
     * @var array
     */
    protected $content;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var string
     */
    protected $delimiter = ',';

    /**
     * @var string
     */
    protected $enclosure = '"';

    /**
     * @param string $filename
     * @param array $header
     * @param array $content
     * @param callable $callback
     * @param string $delimiter
     * @param string $enclosure
     *
     * @return CsvExport|HttpResponse
     * @throws \Exception
     */
    public function __invoke($filename = null,  $header = null, $content = null, callable $callback = null, $delimiter = ',', $enclosure = '"')
    {
        if (func_num_args() == 0) {
            return $this;
        } elseif (func_num_args() == 1) {
            return $this->setFilename($filename);
        }

        return $this
            ->setFilename($filename)
            ->setHeader($header)
            ->setContent($content, $callback)
            ->setControls($delimiter, $enclosure)
            ->getResponse();
    }

    /**
     * Set filename
     *
     * @param $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        if (substr($filename, -4) == '.csv') {
            $filename = substr($filename, 0, -4);
        }
        $this->filename = $filename;

        return $this;
    }

    /**
     * Set header
     *
     * @param $header
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Set content and callback
     *
     * @param $content
     * @param callable|null $callback
     * @return $this
     */
    public function setContent($content, callable $callback = null)
    {
        $this->content  = $content;
        $this->callback = $content;

        return $this;
    }

    /**
     * Set controls
     *
     * @param $delimiter
     * @param $enclosure
     * @return $this
     */
    public function setControls($delimiter, $enclosure)
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * Get response
     *
     * @return HttpResponse
     * @throws \Exception
     */
    public function getResponse()
    {
        if (method_exists($this->controller, 'getResponse')) {
            $response = $this->controller->getResponse();
        } else {
            $response = new HttpResponse;
        }

        $fopen = fopen('php://output', 'w');
        ob_start();
        fputcsv($fopen, $this->header, $this->delimiter, $this->enclosure);
        foreach ($this->content as $key => $value) {
            try {
                $fields = $this->callback ? call_user_func($this->callback, $value) : $value;
                if (! is_array($fields)) {
                    throw new \RuntimeException(
                        'CsvExport can only accept arrays, '
                        . gettype($fields) .' provided at index '
                        . $key
                        .'. Either use arrays when setting the records or use a callback to convert each record into an array.'
                    );
                }
                fputcsv($fopen, $fields, $this->delimiter, $this->enclosure);
            }catch (\Exception $ex) {
                ob_end_clean();
                throw $ex;
            }
        }
        fclose($fopen);

        $response->setContent(ob_get_clean());
        $response->getHeaders()->addHeaders([
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment;filename="' . str_replace('"', '\\"', $this->filename) . '.csv"',
        ]);

        return $response;
    }
}
