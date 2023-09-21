<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;

use Base\Adapter\Db\FormCodeGroupAdapter;

class FormCodeGroupService extends BaseService
{
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\FormCodeGroupAdapter
     */
    protected $adapterForm;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormCodeGroupAdapter $adapterFormCodeGroup)
    {
        $this->config   = $config;
        $this->logger   = $logger;
        $this->adapterFormCodeGroup   = $adapterFormCodeGroup;
    } 
    
    public function getCodePairs($listId)
    {
        return $this->adapterFormCodeGroup->getCodePairs($listId);
    }

    public function insertGroup($desc)
	{
		return $this->adapterFormCodeGroup->insertGroup($desc);
	}
	public function fetchValueListNames($formCodeGroupId){
		return $this->adapterFormCodeGroup->fetchValueListNames($formCodeGroupId);
		
	}
    
    /**
     * Get a lookup map for mapping values to descriptions or descriptions to values for a particular form code list entity type. 
     * @param int $formCodeGroupId
     * @param [string] $formCodeListName ('injuryStatus') any valid code list from form code list table
     * @return array
     * Note: SQL Logic that follows here must match the ReportEntryController _getFormValueLists logic used by keying app.
     */
    public function fetchCodeListMap( $formCodeGroupId, $formCodeListName = 'injuryStatus' )
    {
        try {

            return $this->adapterFormCodeGroup->fetchCodeListMap( $formCodeGroupId, $formCodeListName );

            //@codeCoverageIgnoreStart
        } catch ( Exception $e ) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            eCrash_Logger_Common::logException( $errMsg );
            return false;
        }//@codeCoverageIgnoreEnd
    }

}
