<?php

namespace Irval\AdvancedApprovalQueue\XF\Service\Post;

use XF\Entity\Thread;
use XF\Entity\Forum;

class Editor extends XFCP_Editor
{
    public function setMessage($message, $format = true)
    {
        $visitor = \XF::visitor();
        $thread = $this->post->Thread;

        if ($thread->discussion_state == 'visible' && $this->post->message != $message) {
            if ($this->post->isFirstPost() && $visitor->user_id && !$visitor->hasNodePermission($thread->node_id, 'Irval_aaq_editNoApproval')) {
                $thread->discussion_state = 'moderated';
                $thread->setModeratedDueEdit(true);
                $thread->save();
            }
        }

        return parent::setMessage($message, $format);
    }
}