<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Auth\Controller;

use Zend\Session\Container;
use Zend\View\Helper\HeadTitle;

use Base\Controller\BaseController;

class IndexController extends BaseController
{
    /**
     * @var Zend\Session\Container
     */
    protected $session;
    
    /**
     * @var Zend\View\Helper\HeadTitle
     */
    protected $helperHeadTitle;
    
    public function __construct(
        Container $session,
        HeadTitle $helperHeadTitle)
    {
        $this->session = $session;
        parent::__construct();
        
        $this->view->helperHeadTitle = $helperHeadTitle;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the Welcome Message.
     * @return object   [Zend\View\Model\ViewModel]
     */
    public function indexAction()
    {
        // initializing flag that defines if keying form window will be opened
        // Resetting the report entry popup flag so when user will refresh the page - keying form will not be loaded
        $this->view->showReportEntryPopup = !empty($this->session->showReportEntryPopup);
        $this->session->showReportEntryPopup = false;
        
        $this->view->helperHeadTitle->prepend('Welcome to');
        
        return $this->view;
    }
}
