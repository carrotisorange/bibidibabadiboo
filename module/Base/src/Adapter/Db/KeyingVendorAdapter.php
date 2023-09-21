<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class KeyingVendorAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'keying_vendor';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    /**
     * Get keying vendor details by id
     * @param int $keyingVendorId
     * @return array keying vendor information
     */
    public function getKeyingVendorById($keyingVendorId)
    {
        $select = $this->getSelect();
        $select->where('keying_vendor_id = :keying_vendor_id');

        return $this->fetchRow($select, ['keying_vendor_id' => $keyingVendorId]);
    }
    
    /**
     * Get keying vendor details by name
     * @param string $keyingVendorName
     * @return array keying vendor information
     */
    public function getKeyingVendorByName($keyingVendorName)
    {
        $select = $this->getSelect();
        $select->where('vendor_name = :keying_vendor_name');

        return $this->fetchRow($select, ['keying_vendor_name' => $keyingVendorName]);
    }
    
    /**
     * Get allowed keying vendors
     * @param $excludeList list of vendors to exclude
     * @return array [keying_vendor_id => vendor_name, ...]
     */
    public function getKeyingVendorNamePairs($excludeList = null)
    {
        $columns = [
            'keying_vendor_id' => 'keying_vendor_id',
            'vendor_name' => 'vendor_name',
        ];
        $select = $this->getSelect();
        $select->columns($columns);
        if (!empty($excludeList)) {
            $excludedVendors = implode(",", $excludeList);
            $select->where("vendor_name NOT IN ($excludedVendors)");
        }
        $select->order('keying_vendor_id');
        
        return $this->fetchPairs($select);
    }
}
