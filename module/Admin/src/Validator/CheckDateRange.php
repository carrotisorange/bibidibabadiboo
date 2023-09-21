<?php
/**
 * @copyright   Copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Validator;

use Zend\Validator\AbstractValidator;

class CheckDateRange extends AbstractValidator
{
    const INVALID_DATE_RANGE = 'INVALID_DATE_RANGE';
    
    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID_DATE_RANGE => "The date is outside the valid range."
    ];
    
    /**
     * Constructor to exchange the passed options as validator property
     *
     * @param $options array Provided options from the inputfilter.
     * @return void
     */
    public function __construct(Array $options)
    {
        parent::__construct();
        $this->startDate = $options['from_date'];
        $this->reportDuration = $options['duration'];
    }
    
    /**
     * Check if the element using this validator is valid
     *
     * This method will compare the $value of the element to the other elements
     * it needs to match. If they all match, the method returns true.
     *
     * @param $value string
     * @param $context array All other elements from the form
     * @return boolean Returns true if the element is valid
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $getDifference = strtotime($this->startDate) - strtotime($value);
        
        /**
         * 1 day = 24 hours 
         * 24 * 60 * 60 = 86400 seconds
         */
        $days = abs(round($getDifference / 86400));

        if ($days >= $this->reportDuration) {
            $this->error(self::INVALID_DATE_RANGE);
            return false;
        }
        
        return true;
    }
}