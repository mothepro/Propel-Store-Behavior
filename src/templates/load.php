/**
 * Loads table <?php echo $table; ?> into database
 * @todo Locking storage until load is complete
 */
public static function load(\Propel\Runtime\Connection\PropelPDO $con = null) {
	if(!is_readable('<?php echo addcslashes($file, "'"); ?>'))
		throw new \Exception("Unable to open '<?php echo addcslashes($file, "'"); ?>'.");
	
	if(!isset($con))
		$con = \Propel\Runtime\Propel::getWriteConnection(static::DATABASE_NAME);
		
	$ret = $con->prepare('<?php echo addcslashes($sql, "'"); ?>')->execute();
	
	if($ret)
		fclose( fopen('<?php echo addcslashes($file, "'"); ?>', "w") ); // truncate file
	
	return $ret;
}