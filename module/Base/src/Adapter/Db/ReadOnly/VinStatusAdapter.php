<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\ReadOnly;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

use Base\Adapter\Db\DbAbstract;
use Base\Service\KeyingVendorService;

class VinStatusAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'vin_status';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * Get each status and the count of vins in that status
     *
     * @param int|string $keyingVendorId - keying vendor id 
     * @param string $dateStart - Date to start searching from ( defaults to any )
     * @param string $dateEnd - Date to search until ( defaults to NOW() )
     * @return array - array(array([status][count]),array(...))
     */
    public function fetchVinStatusCount($keyingVendorId, $dateStart = null, $dateEnd = null)
    {
        $where = "WHERE ffc.name = 'vinStatus'";
        $dateStart = $this->normalizeDate($dateStart);
        $dateEnd = $this->normalizeDate($dateEnd);
        if (!empty($dateStart)) {
            $dateStart = $dateStart . ' 00:00:00';
            $where .= " AND ren.date_created BETWEEN '".$dateStart."' AND ";
            if (empty($dateEnd)) {
                $where .= 'NOW()';
            } else {
                $dateEnd = $dateEnd . ' 23:59:59';
                $where .= '"'.$dateEnd.'"';
            }
        } elseif (!empty($dateEnd)) {
            $dateEnd = $dateEnd . ' 23:59:59';
            $where .= " AND ren.date_created < '".$dateEnd."'";
        }
        
        $isKeyingVendorIncluded = false;
        if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $where .= " AND usr.keying_vendor_id = " . $keyingVendorId;
            $isKeyingVendorIncluded = true;
        }
        
        /**
         * Stuff in invalid_vin_queue will be extracted as 'E' and will
         * need to be re-labeled as 'H'. Note, however, that not all 'E' are
         * from the invalid_vin_queue.
         */
        $sql = "
            SELECT
                est.name_internal AS stage,
                rev.value AS status,
                count(ren.report_entry_id) AS count
            FROM form_field_common ffc
            JOIN report_entry_data_value rev
                ON ffc.form_field_common_id = rev.form_field_common_id
            JOIN report_entry ren
                ON rev.report_entry_id = ren.report_entry_id
            JOIN entry_stage est
                ON ren.entry_stage_id = est.entry_stage_id ";
            
        if ($isKeyingVendorIncluded) {
            $sql .= "JOIN user usr 
                     ON ren.user_id=usr.user_id 
                     JOIN keying_vendor kv 
                     ON usr.keying_vendor_id = kv.keying_vendor_id ";
        }
                    
        $sql .= $where . " GROUP BY stage, status";

        return $this->fetchAll($sql);
    }



    public function fetchVinStatusCountByOperator(
        $keyingVendorId,
        $dateStart = null,
        $dateEnd = null,
        $nameFirst = null,
        $nameLast = null)
    {
        $where = "
            WHERE ffc.name = 'vinStatus'";
        
        $dateStart = $this->normalizeDate($dateStart);
        $dateEnd = $this->normalizeDate($dateEnd);
        if (!empty($dateStart)) {

            $dateStart = $dateStart . ' 00:00:00';

            $where .= "\nAND ren.date_created BETWEEN '".$dateStart."' AND ";
            if (empty($dateEnd)) {
                $where .= 'NOW() ';
            } else {
                $dateEnd = $dateEnd . ' 23:59:59';
                $where .= '"'.$dateEnd.'" ';
            }
        } elseif (!empty($dateEnd)) {
            $dateEnd = $dateEnd . ' 23:59:59';
            $where = "\nAND ren.date_created < '".$dateEnd."'  ";
        }
        if (!empty($nameFirst)) {

            $where .= "\nAND usr.name_first = '".$nameFirst."' ";
        }
        if (!empty($nameLast)) {
            $where .= "\nAND usr.name_last = '".$nameLast."' ";
        }
        
        if (!empty($keyingVendorId) && $keyingVendorId != KeyingVendorService::VENDOR_ALL) {
            $where .= "\nAND usr.keying_vendor_id = " . $keyingVendorId . " ";
        }

        $sql = "
            SELECT
                usr.name_first,
                usr.name_last,
                usr.username,
                est.name_internal AS stage,
                rev.value AS status,
                count(ren.report_entry_id) AS count,
                kv.vendor_name AS vendorName
            FROM
                user usr
                JOIN report_entry ren
                    ON usr.user_id = ren.user_id
                JOIN report_entry_data_value rev
                    ON ren.report_entry_id = rev.report_entry_id
                JOIN form_field_common ffc
                    ON rev.form_field_common_id = ffc.form_field_common_id
                JOIN entry_stage est
                    ON ren.entry_stage_id = est.entry_stage_id 
                JOIN keying_vendor kv
                    ON usr.keying_vendor_id = kv.keying_vendor_id 
            $where
            GROUP BY usr.username, stage, status
        ";
        
        return $this->fetchAll($sql);
    }
}
