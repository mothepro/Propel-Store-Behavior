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
		$file  = rtrim($this->getAttribute('dir', sys_get_temp_dir()), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR; // path
		$file .= $this->getTable()->getDatabase()->getName(); // db
		$file .= '-' . $this->getTable()->getName(); // table
	
		return $this->renderTemplate('store', [
			'table'		=> $this->getTable()->getName(),
			'file'		=> $file,
			'args'		=> $args,
		]);
	}
	
	/**
	 * Escapes an argument to be stored in a file
	 * @param mixed $arg
	 * @return string|string[]|null
	 */
	protected static function escape($arg) {
		switch (gettype($arg)) {
			case 'boolean':
				return ($arg ? 1 : 0);

			case 'integer':
			case 'int':
				return intval($arg);

			case 'double':
			case 'float':
				return floatval($arg);

			case 'string':
				return '"' . addcslashes($arg, '\\"') . '"';

			case 'NULL':
			case 'null':
				return '\\N';

			case 'array':
				$ret = [];
				foreach($arg as $k => $a)
					$ret[ $k ] = static::escape ($a);
				return $ret;
			
			case 'object':
				// object can be escaped
				if(method_exsits($arg, 'escape'))
					return static::escape($arg->escape());

				// object has key
				elseif (method_exsits($arg, 'getPrimaryKey'))
					return static::escape($arg->getPrimaryKey());

			
			case 'unknown type':
			case 'resource':
			default:
				return null;
		}
	}

}