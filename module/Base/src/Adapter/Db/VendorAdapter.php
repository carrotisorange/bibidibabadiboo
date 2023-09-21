<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Log\Logger;

use Base\Service\VendorService;

class VendorAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'vendor';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * Fetch all active Vendor Codes and IDs.
     */
    public function fetchActiveVendorPairs()
    {
        $select = $this->getSelect();
        $columns = [
            'vendor_id' => 'vendor_id',
            'vendor_code' => 'vendor_code',
        ];
        $select->columns($columns);
        $select->where('is_active = 1');
        $select->order(['vendor_code'], 'ASC');

        return $this->fetchAll($select);
    }
}
