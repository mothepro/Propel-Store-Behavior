/**
 * Stores a <?php echo $table ?>.
 * Ready to be pushed to a MySQL Table with LOAD DATA
 */
public function store(\Propel\Runtime\Connection\PropelPDO $con = null) {
	$this->preSave($con);
	
	$data = array();
	foreach([<?php echo implode(',', $args); ?>] as $v) {
		$x = static::escape($v);
		
		if(isset($x))
			$data[] = $x;
		else
			throw new \Exception('Cannot log a '. gettype ($arg) .' into table '. $table .' -> '. var_export($arg, true));
	}				
	
	file_put_contents(
		"<?php echo addslashes($file); ?>",
		implode("\t", $data) . PHP_EOL,
		FILE_APPEND
	);
	
	$this->postSave($con);
	
	return $this->getPrimaryKey();
}

protected static function escape($arg) {
	switch (gettype ($arg)) {
		case 'boolean':
			return ($arg ? 1 : 0);

		case 'integer':
		case 'int':
			return intval($arg);

		case 'double':
		case 'float':
			return floatval($arg);

		case 'string':
			return '"'. addcslashes($arg, '\\"') . '"';

		case 'NULL':
			return '\\N';

		case 'object':
			/* object can be escaped
			if(method_exsits($arg, 'escape')) {
				$ret = $arg->escape();
				if(is_array($ret) && count($ret) === 1)
					return static::escape($ret);
				
				
				foreach( as $prim)
				return $arg->store();
				break;
			}
			*/
			
			// object has key
			if(method_exsits($arg, 'getPrimaryKey'))
				return static::escape($arg->getPrimaryKey());

		case 'array':
		case 'unknown type':
		case 'resource':
		default:
			return null;
	}
}