<?php

namespace Irval\AdvancedApprovalQueue\XF\ApprovalQueue;

class Post extends XFCP_Post
{
	public function actionDelete(\XF\Entity\Post $post)
	{
		$reason = $this->getInput('reason', $post->post_id);
		$deleter = \XF::app()->service('XF:Post\Deleter', $post);
		$deleter->delete('soft', $reason);

		$postRepo = \XF::app()->repository('XF:Post');
		$postRepo->sendModeratorActionAlert($post, 'delete', $reason);
	}
}