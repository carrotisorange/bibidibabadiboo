<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Form\Element;
use Zend\Validator;
use InvalidArgumentException;

class BaseController extends AbstractActionController
{
    /*
     * @var Zend\Http\Request
     */
    public $request;
    
    /*
     * @var Zend\View\Model\ViewModel
     */
    public $view;
    
    /*
     * @var Zend\View\Model\JsonModel
     */
    public $json;
    
    public function __construct()
    {
        /**
         * Ensuring that we always have $this->request
         */
        $this->request = $this->getRequest();
        $this->view = new ViewModel();
        $this->json = new JsonModel();
    }
    
    public function indexAction() {
        //return $this->redirect()->toRoute('home');
    }
    
    /**
     * Use when you don't have a form
     * @return string Regenerated CsrfToken Hash value
     */
    protected function getCsrfTokenHash()
    {
        return $this->getCsrfElement()->getCsrfValidator()->getHash(false);
    }
    
    /**
     * Use when you don't have a form (its already on forms)
     * 
     * @return Zend\Form\Element\Csrf;
     */
    protected function getCsrfElement()
    {
        $csrf = new Element\Csrf('csrf');
        $csrf->setAttribute('id', 'csrf');
        $csrf->setOptions([
            'csrf_options' => [
                // @TODO: Use the session expiry time from the config
                'timeout' => 30 * 60, // In seconds, Default value is 300 seconds
                'messages' => [
                    Validator\Csrf::NOT_SAME => 'Token mismatch!',
                ]
            ]
        ]);
        
        return $csrf;
    }
    
    /**
     * Validates a csrf token, intended to be used over ajax to validate the csrf token which is not exists in the form.
     * 
     * @param string $token - csrf hash
     * @return bool - Whether the hash is valid or not.
     */
    protected function validateCsrfToken($token)
    {
        return $this->getCsrfElement()->getCsrfValidator()->isValid($token);
    }
    
    /**
     * Gets a response to json encode and send back that can be handled by hasCsrfError in global.js
     * 
     * @return array
     */
    protected function getInvalidCSRFJsonResponse()
    {
        return ['csrferror' => true];
    }
    
    /**
     * Holds a submitted token for use after a page redirect (the redirect does a submit and needs the pre-redirect token)
     * 
     * @see _getAndUnsetCsrfRedirectToken
     * @param string $token
     */
    protected function setCsrfRedirectToken($token)
    {
        // @TODO: Session value will be updated through session container
        $_SESSION['redirectCsrfToken'] = $token;
    }
    
    /**
     * Returns the CSRF redirect token and unsets it
     * 
     * @see setCsrfRedirectToken
     * @return string
     */
    protected function getAndUnsetCsrfRedirectToken()
    {
        // @TODO: Session value will be updated through session container
        $token = isset($_SESSION['redirectCsrfToken']) ? $_SESSION['redirectCsrfToken'] : null;
        unset($_SESSION['redirectCsrfToken']);
        
        return $token;
    }
    
    protected function addFormMessages($form)
    {
        $messagesForm = $form->getMessages();
        // getting only unique error messages
        $messagesForm = array_intersect_key($messagesForm, array_unique(array_map('serialize', $messagesForm)));
        foreach ($messagesForm as $element => $messagesElement) {
            foreach ($messagesElement as $message) {
                if (!empty($message)) {
                    $this->flashMessenger()->addErrorMessage(htmlentities($message));
                }
            }
        }
        
        //Removed in ZF3
        // @TODO: Will be removed or replaced in future
        //$form->clearErrorMessages();
    }
    
    /**
     * Deals with forward slashes (from dates etc) that will mess with apache
     *
     * @param array $paginatorParams
     * @return array
     */
    protected function escapePaginatorParams($paginatorParams)
    {
        foreach ($paginatorParams as $key => $value) {
            // Apache doesn't allow any forward slashes (even encoded) in the path of the URL.
            $paginatorParams[$key] = str_replace('/', '-', $value);
        }
        
        return $paginatorParams;
    }
    
    /**
     * To get the html condent from the template
     *
     * @param string $viewTemplate Viw template file
     * @param array  $data         Data to generate the html content. If not passed then variable array will be retrieved from current view model.
     * @return string              Html content
     */
    protected function render($viewTemplate, $data = []) {
        if (empty($this->renderer)) {
            throw new InvalidArgumentException('Could not find the Renderer. PhpRenderer should be injected through the controller factory!');
        } elseif (empty($viewTemplate)) {
            // No content for empty template
            return '';
        }
        
        if (empty($data)) {
            // Retrieve the view variable container from the base view model
            $data = $this->view->getVariables();
        }
        
        $view = new ViewModel();
        $view->setTemplate($viewTemplate);
        $view->setVariables($data);
        
        return $this->renderer->render($view);
    }
    
    /**
     * To convert the separator of date string
     *
     * @param string $date      Date value
     * @param string $convertFrom
     * @param string $convertTo
     * @return string           Date value
     */
    protected function convertDateSeperator($date, $convertFrom = '-', $convertTo = '/')
    {
        return str_replace($convertFrom, $convertTo, $date);
    }
}
