<?php
namespace Base\View\Helper\DataRenderer;

use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Renderer\RendererInterface as Renderer;
use Spipu\Html2Pdf\Html2Pdf;
use Zend\Paginator\Adapter;
use Zend\Paginator\Paginator;
use Exception;

use Base\View\Helper\BaseHelper;

class ReportMaker extends BaseHelper
{
    const REPORT_FORMAT_PDF = 'pdf';
    const REPORT_FORMAT_XLS = 'xls';
    const REPORT_FORMAT_HTML = 'html';
    const REPORT_FORMAT_TEXT = 'txt';
    const DEFAULT_VIEW_PATH = 'reportview';
    const DEFAULT_PAGINATOR_PATH = 'paginator';
    const DEFAULT_REPORT_NAME = 'Report';
    const DEFAULT_FIELD_CLASS_PREFIX = 'report';

    /**
     * Zend View object
     * @var ViewModel
     */
    protected $view;

    /**
     * Application config array provides access to application config data.
     * @var Config
     */
    protected $config;

    protected $renderer;

    /**
     * Array which contains pathes to views such as paginator, header, main report view, etc.
     * @var array
     */
    protected $path = [];

    public function __construct(Array $config = [], PhpRenderer $renderer, ViewModel $view)
    {
        $this->view = $view;
        $this->config = $config;
        $this->renderer = $renderer;
        $this->path['view'] = (array_key_exists('viewPath', $config)) ?
            $config['viewPath'] : self::DEFAULT_VIEW_PATH;
        $this->path['paginator'] = (array_key_exists('paginatorPath', $config)) ?
            $config['paginatorPath'] : self::DEFAULT_PAGINATOR_PATH;
    }

    /**
     * Depends on format returns generated report or sends it to the browser.
     *
     * @param string $format - format of the report that needs to be generated
     * @param object|Paginator $paginator - data that will be displayed in the report body
     * @param array $columns - columns that need to be present in the report
     * @param array $paginatorParams - parameters that will be sent to page when operator clicks page number
     * @param array $reportHeader - data that will be displayed in the header
     * @param array $extraTableClasses - classes that will be added to the report table
     * @return html or sends data directly to the browser
     */

    public function output(
        $format,
        $paginator,
        Array $columns = null,
        Array $paginatorParams = null,
        Array $reportHeader = null,
        Array $extraTableClasses = [],
        Array $fieldsToBeAccessed = [],
        $tableId = null)
    {
        if (empty($format) || empty($paginator)) {
            return false;
        }
        
        $reportName = (!empty($reportHeader['reportName'])) ?
            $reportHeader['reportName'] :
            self::DEFAULT_REPORT_NAME;

        if (gettype($paginator) == 'array') {
            $paginator = new Paginator(new Adapter\ArrayAdapter($paginator));
        }

        if ($format == self::REPORT_FORMAT_HTML) {
            $paginator->setItemCountPerPage($this->config['pagination']['perpage']);
        }

        $showPaginator = true;
        
        if ($format != self::REPORT_FORMAT_HTML) {
            $paginator->setItemCountPerPage();
            $showPaginator = false;
        }

        $html = $this->generateHtml(
            $paginator,
            $columns,
            $paginatorParams,
            $reportHeader,
            $extraTableClasses,
            $showPaginator,
            $fieldsToBeAccessed,
            $tableId,
            $format
        );

        switch ($format) {
            case self::REPORT_FORMAT_PDF:
                $pdfOutput = $this->generatePdf($html);
                $this->sendToBrowser($format, $reportName, $pdfOutput);
                break;
            case self::REPORT_FORMAT_XLS:
                $this->sendToBrowser($format, $reportName, $html);
                break;
            case self::REPORT_FORMAT_HTML:
                return $html;
            default:
                throw new Exception('Unknown report format is given : ' . $format);
        }
    }

    /**
     * Generates and returns tabular data.
     *
     * @param object|Paginator $paginator - data that will be displayed in the report body
     * @param uarray $columns - columns that need to be present in the report
     * @param array $paginatorParams - parameters that will be sent to page when operator clicks page number
     * @param array $reportHeader - data that will be displayed in the header
     * @param array $extraTableClasses - classes that will be added to the report table
     * @param bool $showPaginator - flag which indicates whether paginator controls will be displayed
     * @param array $fieldsToBeAccessedRaw - fields which will not be visible on the page (display: none),
     * but will be accessable by javascript
     * @param string $tableId - id of the table
     * @param string $format - output format
     * @return string - output html
     */
    protected function generateHtml(
        $paginator,
        Array $columns = null,
        Array $paginatorParams = null,
        Array $reportHeader = null,
        Array $extraTableClasses = [],
        $showPaginator = true,
        Array $fieldsToBeAccessedRaw = [],
        $tableId = null,
        $format = self::REPORT_FORMAT_HTML)
    {
        if (empty($columns)) {
            $columns = array_keys($paginator->getItem(1));
            $columns = array_combine($columns, $columns);
        }
        
        $fieldsToBeAccessed = [];
        $fieldsToBeAccessedPrefix = null;
        
        if (!empty($fieldsToBeAccessedRaw['fields'])) {
            $fieldsToBeAccessedPrefix = !empty($fieldsToBeAccessedRaw['prefix']) ? $fieldsToBeAccessedRaw['prefix'] : self::DEFAULT_FIELD_CLASS_PREFIX;
            foreach ($fieldsToBeAccessedRaw['fields'] as $field) {
                $fieldsToBeAccessed[$field] = $fieldsToBeAccessedPrefix . '_' . $field;
            }
        }

        $this->view->setTemplate($this->path['view']);
        $this->view->setVariable('tableId', $tableId);
        $this->view->setVariable('fieldsToBeAccessed', $fieldsToBeAccessed);
        $this->view->setVariable('fieldsToBeAccessedPrefix', $fieldsToBeAccessedPrefix);
        $this->view->setVariable('showPaginator', $showPaginator);
        $this->view->setVariable('columns', $columns);
        $this->view->setVariable('paginator', $paginator);
        $this->view->setVariable('paginatorParams', $paginatorParams);
        $this->view->setVariable('extraTableClasses', implode(' ', $extraTableClasses));
        $this->view->setVariable('showReportExtraData', ($format == self::REPORT_FORMAT_HTML));
        $this->view->setVariable('reportHeader',$reportHeader);
        $this->view->setVariable('paginatortemplate',$this->path['paginator']);
        $this->view->setVariable('format', $format);
        
        return $this->renderer->render($this->view);
    }

    protected function generatePdf($html)
    {
        $pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', 3);
        $pdf->setTestTdInOnePage(true);
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->WriteHTML($html);
        
        return $pdf->Output('', 'S');
    }

    public function sendToBrowser($format, $reportName, $data)
    {
        $this->sendHeaders($format, $reportName);
        echo $data;
        exit();
    }

    /**
     * Sends headers to the browser
     *
     * @param string $format - report format
     * @param string $reportName - report name
     */
    private function sendHeaders($format, $reportName)
    {
        if (headers_sent()) {
            throw new Exception(
                "Some data has already been output to browser, " .
                "can't send $reportName.$format file"
            );
        }
        switch ($format) {
            case self::REPORT_FORMAT_PDF:
                $contentType = 'application/x-pdf';
                break;
            case self::REPORT_FORMAT_XLS:
                $contentType = 'vnd.ms-excel';
                break;
            case self::REPORT_FORMAT_TEXT:
                $contentType = 'text/plain';
                break;
            default:
                return false;
        }
        
        header('Content-Description: File Transfer');
        header("Content-type: $contentType");
        header("Content-Disposition: attachment; filename = \"$reportName.$format\"");
        header("Pragma: private");
        header("Cache-control: private, must-revalidate");
    }
    
}
?>