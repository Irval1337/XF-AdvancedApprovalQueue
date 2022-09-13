<?php

namespace Irval\AdvancedApprovalQueue;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Setup extends \XF\AddOn\AbstractSetup
{
	public function install(array $stepParams = [])
    {
        $this->schemaManager()->alterTable('xf_thread', function(Alter $table)
        {
            $table->addColumn('irval_aaq_moderated_due_edit', 'bool')->setDefault(0);
        });
    }

    public function uninstall(array $stepParams = [])
    {
		$this->schemaManager()->alterTable('xf_thread', function(Alter $table)
        {
            $table->dropColumns(['irval_aaq_moderated_due_edit']);
        });
    }

    public function upgrade(array $stepParams = [])
    {
        if ($this->addOn->version_id < 2000000)
        {
			$this->schemaManager()->alterTable('xf_thread', function(Alter $table)
			{
				$table->addColumn('irval_aaq_moderated_due_edit', 'bool')->setDefault(0);
			});
        }
    }
}