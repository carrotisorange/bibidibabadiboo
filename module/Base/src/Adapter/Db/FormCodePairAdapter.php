<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

use Base\Service\EntryStageService;

class FormCodePairAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'form_code_pair';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

   public function insertPair($code, $value)
    {
        //check for existing code/desc pair to use
        $select = $this->getSelect();
        $select->columns(['formCodePairId' => "form_code_pair_id"]);
        $select->where(['code' => $code]);
        $select->where(['description' => $value]);
        $row = $this->fetchRow($select);
        
        if (count($row) > 0) {  
            return $row['formCodePairId'];
        }
        else {
            return $this->insert([
                'code' => $code,
                'description' => $value
            ]);
        
            
        }
    }
    
    public function updatePair($codePairId, $code, $value)
    {
        $data = [];
        
        if (!is_null($code)) {
            $data['code'] = $code;
        }
        if (!is_null($value)) {
            $data['description'] = $value;
        }
        
        return $this->update(
            $data,
            [
                'form_code_pair_id' => $codePairId
            ]
        );
    }
    
    public function deletePair($codePairId)
    {
        return $this->delete([
            'form_code_pair_id' => $codePairId
        ]);
    }
}
