<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class RekeyUserFormPermissionAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'rekey_user_form_permission';
    
    public function getUserFormPermissions($userId, $keyingType)
    {
        $formIds = null;
        $select = $this->getSelect()
            ->where('user_id = :user_id')
            ->where('type = :type');
        $bind = [
            'user_id' => $userId,
            'type' => $keyingType
        ];
        $rows = $this->fetchAll($select, $bind);
        if (count($rows)>0) {
            foreach ($rows as $row) {
                $formIds[] = $row['form_id'];
            }
        }
        
        return $formIds;
    }
    
    public function findIfFormForUser($userId, $formId)
    {
        $formIds = null;
        $select = $this->getSelect()
            ->where('user_id = :user_id')
            ->where('form_id = :form_id');
        $bind = [
            'user_id' => $userId,
            'form_id' => $formId
        ];
        $rows = $this->fetchAll($select, $bind);
        if (count($rows) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
