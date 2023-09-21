<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;

use Base\Adapter\Db\FormNoteAdapter;

class FormNoteService extends BaseService
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
     * @var Base\Adapter\Db\FormNoteAdapter
     */
    protected $adapterForm;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormNoteAdapter $adapterFormNote)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterFormNote = $adapterFormNote;
    }

    public function insertNote($formId, $userId, $note)
    {
        if (empty($userId)) {
            return 0;
        }
        
        return $this->adapterFormNote->insertNote($formId, $userId, $note);
    }

    public function fetchNotes($formId)
    {
        return $this->adapterFormNote->fetchNotes($formId);
    }
    
}
