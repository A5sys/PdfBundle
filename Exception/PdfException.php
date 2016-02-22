<?php

namespace A5sys\PdfBundle\Exception;

/**
 * Classe used when the pdf generation fails.
 */
class PdfException extends \Exception
{
    /**
     * Data we want to embed with the exception.
     *
     * @var mixed
     */
    private $embbedData;

    /**
     * setEmbbedData.
     *
     * @param $embbedData
     */
    public function setEmbbedData($embbedData)
    {
        $this->embbedData = $embbedData;
    }

    /**
     * getEmbbedData.
     *
     * @return mixed
     */
    public function getEmbbedData()
    {
        return $this->embbedData;
    }
}
