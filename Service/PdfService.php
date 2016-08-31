<?php

namespace A5sys\PdfBundle\Service;

use A5sys\PdfBundle\Exception\PdfException;
use mikehaertl\wkhtmlto\Pdf;

/**
 * PdfService. Wkhtmltopdf doit être installé sur le système
 * Utilitaires pour générer des documents PDF.
 */
class PdfService
{
    /**
     * IoC wkhtmltopdf binary path.
     */
    protected $binary;

    /**
     * Temp dir.
     */
    protected $tempDir;

    /**
     * Command option.
     */
    protected $commandOptions;

    /**
     * Encoding.
     */
    protected $encoding;

    /**
     * Constructor.
     */
    public function __construct(
        $binary,
        $tempDir,
        $commandOptions,
        $encoding
    ) {
        $this->binary = $binary;
        $this->tempDir = $tempDir;
        $this->commandOptions = $commandOptions;
        $this->encoding = $encoding;
    }

    /**
     * Returns complete options for PDF generation, with default values from conf eventually overriden by thos of the 3 given arrays.
     *
     * @param array $options        all options, can contain commandOptions
     * @param array $commandOptions all command options, can contain procOptions
     * @param array $procOptions    all process options (system)
     *
     * @return array
     */
    public function getOptions($options = null, $commandOptions = null, $procOptions = null)
    {
        // valeurs par défaut
        $mergedOptions = array(
            'binary' => $this->binary,
            'tmpDir' => $this->tempDir,
            'encoding' => $this->encoding,
        );

        if ($options) {
            $mergedOptions = array_merge_recursive($mergedOptions, $options);
        }

        // Default commandOptions
        $mergedCommandOptions = array();
        if (isset($this->commandOptions['use_exec'])) {
            $mergedCommandOptions['useExec'] = $this->commandOptions['use_exec'];
        }
        if (isset($this->commandOptions['escape_args'])) {
            $mergedCommandOptions['escapeArgs'] = $this->commandOptions['escape_args'];
        }

        if (isset($this->commandOptions['proc_options'])) {
            $mergedCommandOptions['procOptions'] = $this->commandOptions['proc_options'];
        }

        // merge if needed
        if ($commandOptions) {
            $mergedCommandOptions = array_merge_recursive($mergedCommandOptions, $commandOptions);
        }

        // update parent array
        $mergedOptions['commandOptions'] = $mergedCommandOptions;

        // update procOptions only, if it is needed
        if ($procOptions) {
            // default values, eventually already updated by 'options' or 'commandOptions', but will be overriden by thos given in param
            if (isset($mergedCommandOptions['proc_options'])) {
                $mergedProcOptions = $mergedCommandOptions['proc_options'];
            } else {
                $mergedProcOptions = array();
            }

            $mergedProcOptions = array_merge_recursive($mergedProcOptions, $procOptions);

            $mergedCommandOptions['procOptions'] = $mergedProcOptions;
            $mergedOptions['commandOptions'] = $mergedCommandOptions;
        }

        return $mergedOptions;
    }

    /**
     * Save the generated PDF in the specified path.
     *
     * @param type $filePath
     * @param type $html           HTML to render in PDF
     * @param type $options        Can contain informations for header et footer (header-html, footer-html, footer-left ... use "wkhtmltopdf.exe -H" for more help)
     * @param type $commandOptions
     * @param type $procOptions
     *
     * @return type
     */
    public function saveAs($filePath, $html, $options = null, $commandOptions = null, $procOptions = null)
    {
        $options = $this->getOptions($options, $commandOptions, $procOptions);

        $pdf = new Pdf($options);

        $pdf->addPage($html);

        if (!$pdf->saveAs($filePath)) {
            throw new PdfException('Could not create PDF: '.$pdf->getError());
        }
    }

    /**
     * Send the generated PDF onto the Response
     *
     * @param type $html           HTML to render in PDF
     * @param type $fileName
     * @param type $options        Can contain informations for header et footer (header-html, footer-html, footer-left ... use "wkhtmltopdf.exe -H" for more help)
     * @param type $commandOptions
     * @param type $procOptions
     *
     * @return type
     */
    public function sendPDF($html, $fileName, $options = null, $commandOptions = null, $procOptions = null)
    {
        $options = $this->getOptions($options, $commandOptions, $procOptions);

        $pdf = new Pdf($options);

        $pdf->addPage($html);

        if (!$pdf->send($fileName)) {
            throw new PdfException('Could not create PDF: '.$pdf->getError());
        }
    }
}
