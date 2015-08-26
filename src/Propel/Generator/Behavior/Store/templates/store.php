/**
 * Stores a <?php echo $table ?>.
 * Ready to be pushed to a MySQL Table with LOAD DATA
 */
public function store(\Propel\Runtime\Connection\PropelPDO $con) {
	$this->preSave($con);
					
	file_put_contents(
		"<?php echo addcslashes($file, '"'); ?>",
		vsprintf(
			"<?php echo addcslashes($format, '"'); ?>",
			[<?php echo implode(',', $args); ?>]
		),
		FILE_APPEND
	);
	
	$this->postSave($con);
}