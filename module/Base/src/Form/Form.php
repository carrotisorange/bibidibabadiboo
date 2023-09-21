<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Form;

use Zend\Form\Form as ZendForm;
use Zend\Form\Element;
use Zend\Validator\Csrf;

/**
 * Common form class
 */
class Form extends ZendForm
{
    /**
     * Constructor for the base form which all forms should be extended
     * @param string    $name   Name of the form
     * @param array     $options Form options if any
     */
    public function __construct($name = null, $options = [], $hasCsrf = true)
    {
        parent::__construct($name, $options);
        
        if ($hasCsrf) {
            $this->addCSRF();
        }
    }
    
    /**
     * Adds the csrf element to the form.
     * 
     * This should ideally be used on all forms; you will have to manually add this to the view if you do not use the 
     * form for layout.
     */
    protected function addCSRF()
    {
        $csrf = new Element\Csrf();
        $csrf->setName('csrf');
        $csrf->setAttributes([
            'id' => 'csrf'
        ]);
        
        $csrf->setOptions([
            'csrf_options' => [
                // @TODO: Use the session expiry time from the config
                'timeout' => 30 * 60, // In seconds, Default value is 300 seconds
                'messages' => [
                    Csrf::NOT_SAME => 'Token mismatch!',
                ]
            ]
        ]);
        
        $this->add($csrf);
    }
}
