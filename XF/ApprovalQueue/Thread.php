<?php

namespace Irval\AdvancedApprovalQueue\XF\ApprovalQueue;

class Thread extends XFCP_Thread
{
	public function actionDelete(\XF\Entity\Thread $thread)
	{
		if ($thread->isModeratedDueEdit()) {
			$historyFinder = \XF::app()->finder('XF:EditHistory')
				->where('content_id', $thread->first_post_id)
				->order('edit_date', 'DESC')
				->order('edit_history_id', 'DESC');
			
			$count = $historyFinder->total();
			if (!$count)
			{
				return;
			}

			$historyRepo = \XF::app()->repository('XF:EditHistory');
			$historyRepo->revertToHistory($historyFinder->fetchOne());

			$thread->discussion_state = 'visible';
			$thread->setModeratedDueEdit(false);
			$thread->save();

			$move = $this->getInput('move', $thread->thread_id);
			if ($move) {
				$trashForum = \XF::em()->find('XF:Forum', \XF::options()->Irval_aaq_archive_forum);

				if (!$trashForum)
				{
					return;
				}

				$mover = \XF::app()->service('XF:Thread\Mover', $thread);
				$mover->setNotifyWatchers(false);
				$mover->setSendAlert(false);
				$mover->move($trashForum);
			}
		}
		else {
			$reason = $this->getInput('reason', $thread->thread_id);
			$deleter = \XF::app()->service('XF:Thread\Deleter', $thread);
			$deleter->delete('soft', $reason);

			$threadRepo = \XF::app()->repository('XF:Thread');
			$threadRepo->sendModeratorActionAlert($thread, 'delete', $reason);
		}
	}

	public function actionApprove(\XF\Entity\Thread $thread)
	{
		if ($thread->isModeratedDueEdit()) {
			parent::actionApprove($thread);
		}
		else {
			$thread->discussion_state = 'visible';
		}
		$thread->setModeratedDueEdit(false);
		$thread->save();

		$move = $this->getInput('move', $thread->thread_id);
		if ($move) {
			$trashForum = \XF::em()->find('XF:Forum', \XF::options()->Irval_aaq_archive_forum);

			if (!$trashForum)
			{
				return;
			}

			$mover = \XF::app()->service('XF:Thread\Mover', $thread);
			$mover->setNotifyWatchers(false);
			$mover->setSendAlert(false);
			$mover->move($trashForum);
		}
	}
}
