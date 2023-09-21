<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Exception;

/**
 * Abstract class for database adapters
 *
 * @package Base\Adapter\Db
 */
abstract class DbAbstract
{
    const DB_TYPE_MYSQL = 'mysql';
    
    /**
     * @var DbAdapter
     */
    protected $dbAdapter;
    
    /**
     * @var select
     */
    protected $select;
    
    /**
     * @var string
     */
    protected $table;
    
    /**
     * @var Zend\Db\Sql\Sql
     */
    protected $sql;
    
    /**
     * Primary key
     * @var string
     */
    protected $pk;
    
    /**
     * @var Base\Service\MailerService
     */
    protected $serviceMailer;
    
    /**
     * DbAbstract constructor.
     *
     * @param DbAdapter $dbAdapter
     * @param string $table
     */
    public function __construct(
        DbAdapter $dbAdapter,
        $table = null)
    {
        /*
         * It's possible to set initialize $this->table in the child DB adapters so $table is not a required parameter.
         * At this point I don't even see the reason why we need to pass $table here since every DB adapter should have it
         * initialized.
         */
        if (!empty($table)) {
            $this->table = $table;
        }
        
        $this->dbAdapter = $dbAdapter;
        
        $this->getSelect();
    }
    
    protected function getSelect()
    {
        $this->sql  = new Sql($this->dbAdapter);
        $this->select = $this->sql->select();
        if (!empty($this->table)) {
            $this->select->from($this->table);
        }
        return $this->select;
    }
    
    protected function getWhere()
    {
        return new Where();
    }
    
    public function getAdapter()
    {
        return $this->dbAdapter;
    }
    
    /**
     * Checks to see if current db link is mysql
     *
     * @return boolean
     */
    public function isMySql()
    {
        return ($this->getPdoType() == self::DB_TYPE_MYSQL);
    }
    
    /**
     * Gets PDO Type
     *
     * @return string
     */
    protected function getPdoType()
    {
        return $this->dbAdapter->getDriver()->getConnection()->getDriverName();
    }
    
    public function getCurrentDbUser()
    {
        $dbConf = $this->dbAdapter->getDriver()->getConnection()->getConnectionParameters();
        return $dbConf['username'];
    }
    
    public function getIntExpr($int, $returnAsString = false)
    {
        $int = (int) $int;
        
        if ($this->isMySql()) {
            return $int;
        } elseif ($this->isSybase()) {
            $result = new Expression("convert(int, $int)");
            if ($returnAsString) {
                $result = $result->getExpression();
            }
            
            return $result;
        }
        
        throw new Exception('Unknown adapter type');
    }
    
    public function getNowExpr()
    {
        $exprVal = ($this->isMySql()) ? 'NOW()' : 'GETDATE()';
        return new Expression($exprVal);
    }
    
    public function getDecimalExpr($val)
    {
        if ($this->isMySql()) {
            return $val;
        } elseif ($this->isSybase()) {
            return new Expression("convert(decimal, $val)");
        }
        
        throw new Exception('Unknown adapter type');
    }

    public function getBigintExpr($bigInt)
    {
        $bigInt = (int) $bigInt;
        if ($this->isMySql()) {
            return $bigInt;
        } elseif ($this->isSybase()) {
            return new Expression("convert(BIGINT, $bigInt)");
        }
        
        throw new Exception('Unknown adapter type');
    }
    
    public function getAddToCurDateExpr($days)
    {
        if (empty($days)) {
            return;
        }
        
        $days = (int)$days;
        
        if ($this->isMySql()) {
            $expression = "DATE_ADD(NOW(), INTERVAL $days DAY)";
        } elseif ($this->isSybase()) {
            $expression = "DATEADD(dd, $days, GETDATE())";
        } else {
            throw new Exception('Unknown adapter type');
        }
        
        return new Expression($expression);
    }
    
    /**
     * Fetches all SQL result rows as a sequential array
     * @param string|object|null $select [Select prepared query] | [Zend\Db\Sql\Select]
     * @param array              $bind   Bind parameters of the query
     * @return array                     [Results]
     */
    protected function fetchAll($select = null, Array $bind = null)
    {
        if (empty($select)) {
            $select = $this->getSelect();
        }
        
        $data = [];
        $resultSet = $this->fetchResultSet($select, $bind);
        if (!empty($resultSet) && is_array($resultSet)) {
            foreach ($resultSet as $result) {
                $data[] = $result;
            }
        }
        
        return $data;
    }
    
    public function fetchArray($resultSet)
    {
        return $resultSet->toArray();
    }
    
    /**
     * [fetchResultSet description]
     * @param  string/object    $select [description]
     * @param  array|null       $bind   [description]
     * @return array
     */
    private function fetchResultSet($select, Array $bind = null)
    {
        if (($select instanceof \Zend\Db\Sql\Select) && (!empty($bind))) {
            $select = $this->sql->getSqlStringForSqlObject($select);
            $resultSet = $this->dbAdapter->query($select, $bind)->toArray();
        } elseif (!empty($bind)) {
            $resultSet = $this->dbAdapter->query($select, $bind)->toArray();
        } elseif ($select instanceof \Zend\Db\Sql\Select) {
            $selectString = $this->sql->getSqlStringForSqlObject($select);
            $resultSet = $this->dbAdapter->query(
                $selectString,
                $this->dbAdapter::QUERY_MODE_EXECUTE
            )->toArray();
        } else {
            $resultSet = $this->dbAdapter->query($select, $this->dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        }
        
        return $resultSet;
    }
    
    /**
     * Returns the first column of the first row of the resultset
     * @param string|object $select [Select prepared query] | [Zend\Db\Sql\Select]
     * @param array         $bind   Bind parameters of the query
     * @return array                [Results]
     */
    protected function fetchOne($select, Array $bind = null)
    {
        if (empty($select)) {
            return;
        }
        
        $data = [];
        $resultSet = $this->fetchResultSet($select, $bind);
        if (!empty($resultSet) && is_array($resultSet)) {
            $data = reset($resultSet[0]);
        }
        
        return $data;
    }
    
    /**
     * Fetches the first row of the SQL result
     * @param string|object $select [Select prepared query] | [Zend\Db\Sql\Select]
     * @param array         $bind   Bind parameters of the query
     * @return array                [Results]
     */
    protected function fetchRow($select, Array $bind = null)
    {
        if (empty($select)) {
            return;
        }
        
        $data = [];
        $resultSet = $this->fetchResultSet($select, $bind);
        if (!empty($resultSet) && is_array($resultSet)) {
            $data = $resultSet[0];
        }
        
        return $data;
    }

    /**
     * [fetchPairs - fetch two rows from sql and combine as key - value pair]
     * @param string|object $select [Select prepared query] | [Zend\Db\Sql\Select]
     * @param array         $bind   Bind parameters of the query
     * @return array                [Results]
     */
    protected function fetchPairs($select, Array $bind = null)
    {
        if (empty($select)) {
            return;
        }
        
        $data = [];
        $resultSet = $this->fetchResultSet($select, $bind);
        if (!empty($resultSet) && is_array($resultSet)) {
            foreach ($resultSet as $result) {
                $values = array_values($result);
                $data[$values[0]] = $values[1];
            }
        }
        
        return $data;
    }
    
    /**
     * Fetches the first column of all SQL result rows as an array.
     * @param  object|string $select [description]
     * @param  array|null    $bind   [description]
     * @return array                    [description]
     */
    protected function fetchCol($select, Array $bind = null)
    {
        if (empty($select)) {
            return;
        }
        
        $data = [];
        $resultSet = $this->fetchResultSet($select, $bind);
        if (!empty($resultSet)) {
            foreach ($resultSet as $result) {
                $data[] = reset($result);
            }
        }
        
        return $data;
    }
    
    /**
     * Insert data into Table.
     * Examples of usages:
     * 1. $this->insert($data) - this will insert $data into $this->table
     * 2. $this->insert('tableName', $data) - this will insert $data into 'tableName'
     *
     * @param string|array $table either table name or the data that has to be inserted
     * @param array data that has to be inserted
     * @return int last inserted id
     */
    public function insert($table, Array $data = null)
    {
        /**
         * If $table contains array it means it's data for inserting. In that case we use $this->table as table.
         */
        if (is_array($table) && empty($data)) {
            $data = $table;
            $table = $this->table;
        }
        $ins = $this->sql->insert()->into($table)->values($data);
        $insertObj = $this->sql->prepareStatementForSqlObject($ins);
        $insertObj->execute();
        
        /**
         * If primary key supplied in the data than we need to use it, otherwise using getLastGeneratedValue to get it.
         * If the primary key is an array of keys, then we just use the getLastGeneratedValue
         */
        if (is_array($this->pk) || !isset($data[$this->pk])) {
            $return = $this->dbAdapter->getDriver()->getLastGeneratedValue();
        } else {
            $return = $data[$this->pk];
        }
        
        return $return;
    }
    
    /**
     * Update data into Table
     * @param   [type]  $data   [array data]
     * @param   [type]  $where  [array condition]
     * @return  [type]  [affected flag]
     */
    public function update(Array $data, $where)
    {
        $update = $this->sql->update();
        $update->table($this->table);
        $update->set($data);
        $update->where($where);
        
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        if (!$result->getAffectedRows()) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Delete data from Table
     *
     * @param string|array  $table Can be table name or where condition
     * @param array|null    $where Can we where condition or null
     * @return bool
     */
    public function delete($table, Array $where = null)
    {
        if (is_array($table) && empty($where)) {
            $where = $table;
            $table = $this->table;
        }
        
        $delete = $this->sql->delete();
        $delete->from($table);
        $delete->where($where);
        
        $statement = $this->sql->prepareStatementForSqlObject($delete);
        $result = $statement->execute();
        
        // Find the if the delete has been successfull
        if (!$result->getAffectedRows()) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * getUnixTimestamp from the database
     * @param  string $value datetime
     * @return string
     */
    public function getUnixTimestamp($value)
    {
        $query = "SELECT UNIX_TIMESTAMP(:value) AS `unix_timestamp`";
        $bind = ['value' => $value];
        return $this->fetchRow($query, $bind)['unix_timestamp'];
    }
    
    public function getFormattedTimeString($string)
    {
        $return = null;
        if (!empty($string)) {
            $return = $this->getFormattedTimestamp(strtotime($string));
        }
        
        return $return;
    }
    
    /**
     * Will format a timestamp to be compatible with mysql
     *
     * @param int $timestamp - See time()
     * @return string
     */
    public function getFormattedTimestamp($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }
    
    /**
     * Some basic date (with no time) validation & normalization using strtotime
     *
     * @param string $dateString
     * @return FALSE|string - FALSE if strtotime fails otherwise Y-m-d
     */
    public function normalizeDate($dateString)
    {
        $timestamp = strtotime($dateString);
        
        return (!empty($timestamp)) ? date('Y-m-d', $timestamp) : false;
    }
    
    /**
     * To get the number of rows in the result set
     * @param string|object $select [Select prepared query] | [Zend\Db\Sql\Select]
     * @param array         $bind   Bind parameters of the query
     * @return int                  Number of rows
     */
    protected function rowCount($select, Array $bind = null)
    {
        if (empty($select)) {
            return 0;
        }
        
        $resultSet = $this->fetchResultSet($select, $bind);
        if ((!empty($resultSet)) && (is_array($resultSet))) {
            return count($resultSet);
        } else {
            return 0;
        }
    }
    
    /**
     * Fetches all SQL result rows as a associative array
     * @param string|object $select [Select prepared query] | [Zend\Db\Sql\Select]
     * @param array         $bind   Bind parameters of the query
     * @return array                [Results]
     */
    protected function fetchAssoc($select, Array $bind = null)
    {
        if (empty($select)) {
            return;
        }
        
        $data = [];
        $resultSet = $this->fetchResultSet($select, $bind);
        if ((!empty($resultSet)) && (is_array($resultSet))) {
            foreach ($resultSet as $result) {
                $data[array_values($result)[0]] = $result;
            }
        }
        
        return $data;
    }
    
    public function getDistinct($column)
    {
        return new Expression('DISTINCT ' . $column);
    }
    
    public function getCount($column = null)
    {
        if (!empty($column)) {
            return new Expression('COUNT(' . $column . ')');
        } else {
            return new Expression('COUNT(*)');
        }
    }
    
    public function getMax($column)
    {
        return new Expression("MAX(" . $column . ")");
    }
    
    public function getGroupConcatExpr($column, $separator = ',')
    {
        return new Expression('GROUP_CONCAT(' . $column . ' SEPARATOR "' . $separator . '")');
    }
	
	/**
	 * Return status of PDO object query operation in boolean form
	 * @return bool
	 */
	public static function pdoQueryOperationStatus($pdo)
	{
		try {
			$resource = $pdo->getResource();
			$errCode = $resource->errorCode();// 00000
			
			if (is_numeric($errCode) && intval($errCode) === 0) {
				$results = true;
			} else {
				$results = false;
			}
			return $results;
			// @codeCoverageIgnoreStart
		} catch (\Exception $e) {
			
			 //@TODO: Error Log to be loggedd
			
			//return false;
		}// @codeCoverageIgnoreEnd
	}
    
}

