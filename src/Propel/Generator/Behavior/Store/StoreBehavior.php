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
		$col = $format = $args = array();
		foreach($this->getTable()->getColumns() as $column) {
			$col[ $column->getName() ] = $column->getPhpType();
			$args[] = '$this->get'. $column->getPhpName() .'()';
		}
//		ksort($col);
		
		foreach($col as $name => $type)
			switch ($type) {
			case 'boolean':
				$format[] = '%d';
				break;

			case 'integer':
			case 'int':
				$format[] = '%d';
				break;

			case 'double':
				$format[] = '%f';
				break;

			case 'string':
				$format[] = '"%s"';
				break;

			case 'NULL':
				$format[] = '\\N';
				break;

			case 'object':
			default:
				// the object uses storable
				if(class_exists($type) && in_array('Propel\Generator\Behavior\Store\Storable', class_implements($type))) {
					
					break;
				} // fallthru
						
			case 'array':
			case 'unknown type':
			case 'resource':
				throw new \Exception('Cannot log a '. $type .' into table '. $this->getTable()->getName());
			}
		//addcslashes(%s, '"') 
		
		
		$x = [
			'table'		=> $this->getTable()->getName(),
			'file'		=> $this->getAttribute('dir', sys_get_temp_dir()) . DIRECTORY_SEPARATOR . $this->getTable()->getDatabase()->getName() .'.'. $this->getTable()->getName(),
			'format'	=> implode('\t', $format) . '\n',
			'args'		=> $args,
		];
		
		var_dump($x);
		echo $this->renderTemplate('store', $x);
		return $this->renderTemplate('store', $x);
	}
}
