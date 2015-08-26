<?php
namespace Propel\Generator\Behavior\Store;

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
		// all columns
		$args = array();
		foreach($this->getTable()->getColumns() as $column)
			$args[] = '$this->get'. $column->getPhpName() .'()';
		
		// working file
		$file  = $this->getAttribute('dir', sys_get_temp_dir() . DIRECTORY_SEPARATOR); // path
		$file .= $this->getTable()->getDatabase()->getName(); // db
		$file .= '-' . $this->getTable()->getName(); // table
	
		return $this->renderTemplate('store', [
			'table'		=> $this->getTable()->getName(),
			'file'		=> $file,
			'args'		=> $args,
		]);
	}
}
