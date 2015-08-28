/**
 * Stores a <?php echo $table ?>.
 * Ready to be pushed to a MySQL Table with LOAD DATA
 */
public function store(\Propel\Runtime\Connection\PropelPDO $con = null) {
	$this->preSave($con);
	
	$data = array();
	foreach([<?php echo implode(',', $args); ?>] as $v) {
		$x = \Propel\Generator\Behavior\Store\StoreBehavior::escape($v);
		
		switch(gettype($x)) {
		case 'string':
			$data[] = $x;
			break;
		
		case 'array':
			foreach($x as $xx)
				$data[] = $xx;
			break;
			
		default:
			throw new \Exception(sprintf('Cannot log a %s into table "<?php echo $table ?>"', gettype($x)));
		}
	}		
	
	file_put_contents(
		"<?php echo addslashes($file); ?>",
		implode("\t", $data) . PHP_EOL,
		FILE_APPEND
	);
	
	$this->postSave($con);
}