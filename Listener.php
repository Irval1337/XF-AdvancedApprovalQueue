<?php

namespace Irval\AdvancedApprovalQueue;

use XF\Mvc\Entity\Entity;

class Listener
{
    public static function threadEntityStructure(\XF\Mvc\Entity\Manager $em, \XF\Mvc\Entity\Structure &$structure)
    {
        $structure->columns['irval_aaq_moderated_due_edit'] = ['type' => Entity::BOOL, 'default' => false];
    }
}