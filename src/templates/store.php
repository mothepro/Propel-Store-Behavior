/**
 * Stores a <?php echo $table ?>.
 * Ready to be pushed to a MySQL Table with LOAD DATA
 */
public function store(\Propel\Runtime\Connection\PropelPDO $con) {
	echo '<?php var_dump($attr, $dir, $table, $dir); ?>';
}