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
		'dir'		=> null,
		'control'	=> null,
	];
	
	/**
	 * File we will be working with
	 * @return string
	 */
	protected function getFile() {
		$file  = rtrim($this->getAttribute('dir', sys_get_temp_dir()), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR; // path
		$file .= $this->getTable()->getDatabase()->getName(); // db
		$file .= '-' . $this->getTable()->getName(); // table
		
		return addcslashes($file, '" \\');
	}

	public function objectMethods() {
		$script = '';
		$script .= $this->addStore();
		return $script;
	}

	public function staticMethods() {
		$script = '';
		$script .= $this->addLoad();
		return $script;
	}

	protected function addStore() {
		// all columns
		$args = array();
		foreach($this->getTable()->getColumns() as $column)
			$args[] = '$this->get'. $column->getPhpName() .'()';
		
		return $this->renderTemplate('store', [
			'table'		=> $this->getTable()->getName(),
			'file'		=> $this->getFile(),
			'args'		=> $args,
		]);
	}

	protected function addLoad() {
		// all columns
		$cols = $expr = array();
		foreach($this->getTable()->getColumns() as $column)
			$cols[ $column->getName() ] = sprintf('`%s`', $column->getName());
		
		
		// expressions
		foreach($this->getParameters() as $name => $param) {
			if(in_array($name, array_keys($this->getAttributes())))
				continue;
			
			if(isset($cols[$name])) {
				$varColName		= '@var_' . md5($name); // new sql variable
				
				$expr[] = sprintf('`%s` = %s',
					$name, // column
					str_replace('@', $varColName, $param) // expression
				);
				$cols[$name] = $varColName;
			}
		}
		
		// to string
		$cols	= implode(', ', $cols);
		$expr	= implode(', ', $expr);
		
		// for template
		$file	= $this->getFile();
		$table	= $this->getTable()->getName();
		$sql	= sprintf(<<<'SQL'
	LOAD DATA
		LOW_PRIORITY
		INFILE '%s'
		%s
		INTO TABLE %s
		FIELDS
			TERMINATED BY ''
			ENCLOSED BY '"'
			ESCAPED BY '\\'
		LINES
			STARTING BY ''
			TERMINATED BY '\n'
		IGNORE 0 LINES
		(%s)
		SET %s
SQL
	, $file
	, $this->getAttribute('control', null)
	, $table
	, $cols
	, $expr);
		
		return $this->renderTemplate('load', [
			'table'		=> $table,
			'file'		=> $file,
			'sql'		=> $sql,
		]);
	}
	
	/**
	 * Escapes an argument to be stored in a file
	 * @param mixed $arg
	 * @return string|string[]|null
	 */
	public static function escape($arg) {
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
				if(is_subclass_of($arg, '\Propel\Generator\Behavior\Store\Storable'))
					return static::escape($arg->escape());

				// object has key
				elseif (is_subclass_of($arg, '\Propel\Runtime\ActiveRecord\ActiveRecordInterface'))
					return static::escape($arg->getPrimaryKey());

			
			case 'unknown type':
			case 'resource':
			default:
				return null;
		}
	}
}