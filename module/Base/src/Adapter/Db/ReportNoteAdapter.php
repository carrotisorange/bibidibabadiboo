<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class ReportNoteAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'report_note';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function add($reportId, $userId, $note, $area)
    {
        $data = [
            'date_created' => $this->getNowExpr(),
            'report_id' => $reportId,
            'user_id' => $userId,
            'note' => $note,
            'area' => $area,
        ];

        $this->insert($data);
    }

    public function getReportNotesWithUsers($reportId)
    {
        $select = $this->getSelect()
            ->from(['rn' => $this->table])
            ->columns(['note' => 'note', 'area' => 'area', 'dateCreated' => 'date_created'])
            ->join(['u' => 'user'], 'u.user_id = rn.user_id', ['username' => 'username'], Select::JOIN_LEFT)
            ->where('rn.report_id = :report_id')
            ->order('rn.date_created DESC');
        $bind = ['report_id' => $reportId];

        return $this->fetchAll($select, $bind);
    }

    public function hasReportNotes($reportId)
    {
        $select = $this->getSelect()
            ->columns(['count' => new Expression("COUNT(1)")])
            ->where('report_id = :report_id');
        $bind = ['report_id' => $reportId];
        return ($this->fetchOne($select, $bind) > 0);
    }
}