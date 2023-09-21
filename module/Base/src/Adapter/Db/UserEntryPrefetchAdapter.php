<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Exception;

class UserEntryPrefetchAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'user_entry_prefetch';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(
        Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
        $this->adapter = $adapter;
    }
    
    public function fetchReportIdByUserId($userId)
    {
        $select = $this->getSelect()
            ->from($this->table)
            ->columns(['report_id'])
            ->where('user_id = :user_id')
            ->limit(1);
        
        $bind = ['user_id' => $userId];

        return $this->fetchOne($select, $bind);
    }

    /**
     * Remove reports from a users prefetch queue and reset their report status.
     *
     * @param integer $userId
     */
    public function removeUserReports($userId)
    {
        $this->adapter->getDriver()->getConnection()->beginTransaction();

        try {
            $sql = "
                DELETE uep, re
                FROM user_entry_prefetch AS uep
                    LEFT JOIN report_entry AS re USING (report_id, user_id)
                WHERE uep.user_id = :user_id
            ";
            $bind = ['user_id' => $userId];
            $this->adapter->query($sql, $bind);
            $this->adapter->getDriver()->getConnection()->commit();
        } catch (Exception $e) {
            $this->adapter->getDriver()->getConnection()->rollBack();
            throw $e;
        }
    }
    
    public function remove($reportId, $userId)
    {
        $this->delete([
            'report_id' => $reportId,
            'user_id' => $userId,
        ]);
    }
    
    public function addUserEntry($userId, $reportId)
    {
        $data = [
            'user_id' => $userId,
            'report_id' => $reportId
        ];
        
        return $this->insert($data);
    }
}