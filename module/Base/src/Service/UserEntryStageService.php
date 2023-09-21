<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Db\Sql\Select;

use Base\Adapter\Db\UserEntryStageAdapter;

class UserEntryStageService extends BaseService
{
    /**
     * @var Base\Adapter\Db\UserEntryStageAdapter
     */
    protected $adapterUserEntryStage;
    
    public function __construct(UserEntryStageAdapter $adapterUserEntryStage)
    {
        $this->adapterUserEntryStage = $adapterUserEntryStage;
    }
    
    public function deleteByUserId($userId)
    {
        return $this->adapterUserEntryStage->delete(['user_id' => $userId]);
    }
    
    public function add($fields)
    {
        return $this->adapterUserEntryStage->insert($fields);
    }
    
    public function getEntryStageByUserId($userId)
    {
        return $this->adapterUserEntryStage->getEntryStageByUserId($userId);
    }
}
