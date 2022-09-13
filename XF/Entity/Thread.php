<?php

namespace Irval\AdvancedApprovalQueue\XF\Entity;

use XF\Mvc\Entity\Structure;
use XF\Diff;

/**
 * Class Thread
 *
 * @property bool moderated_due_edit
 */
class Thread extends XFCP_Thread
{
    public function isModeratedDueEdit()
    {
        return $this->irval_aaq_moderated_due_edit;
    }

    public function setModeratedDueEdit($value) {
        $this->irval_aaq_moderated_due_edit = $value;
    }

    public function getDifferences() {
        $newText = $this->FirstPost->message;
        $historyFinder = \XF::app()->finder('XF:EditHistory')
            ->where('content_id', $this->first_post_id)
            ->order('edit_date', 'DESC')
            ->order('edit_history_id', 'DESC');
        
        $count = $historyFinder->total();
        if (!$count)
        {
            return null;
        }

        $historyRepo = \XF::app()->repository('XF:EditHistory');
        $oldText = $historyFinder->fetchOne()->old_text;
        $diffHandler = new Diff();
		$diffs = $diffHandler->findDifferences($oldText, $newText, Diff::DIFF_TYPE_LINE);
        return $diffs;
    }

    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);
        $structure->columns['irval_aaq_moderated_due_edit'] = ['type' => self::BOOL, 'default' => false];

        return $structure;
    }
}