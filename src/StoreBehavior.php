<?php
namespace Propel\Behavior\Store;

use Propel\Generator\Model\Behavior;

/**
 * This behavior adds a store method to objects
 * So they may be placed in a file to be loaded
 * by a cronjob
 *
 * @author Maurice Prosper <maurice.prosper@ttu.edu>
 */
class StoreBehavior extends Behavior {
	/**
	 * Default parameter values
	 * @var string[]
	 */
	protected $parameters = [
		'dir'	=> null, // sys_get_temp_dir(),
	];
	
	public function objectMethods() {
		$script = '';
		$script .= $this->addStore();
		return $script;
	}

	protected function addStore() {
		$dir = $this->getParameter('dir');
		if(empty($dir))
			$dir = sys_get_temp_dir ();
		
		
		return $this->renderTemplate('store', [
			'dir'	=> $dir,
			'table'	=> $this->getTable(),
			'db'	=> $this->getDatabase()->getName(),
			'attr'	=> $this->getAttributes(),
		]);
	}
}
