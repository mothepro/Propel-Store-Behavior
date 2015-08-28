<?php

require 'vendor/autoload.php';
require 'generated-conf/config.php'; // Propel's autoloader

function fail($message, $status = 1) {
	fwrite(STDERR, PHP_EOL . $message . PHP_EOL);
	exit($status);
}

$opt = getopt('n:s:');

if(!isset($opt['n']) && !isset($opt['s']))
	fail('Missing reqiured option');

$namespace = $opt['n'];
$maps = is_array($opt['s']) ? $opt['s'] : array($opt['s']);

foreach($maps as $map) {
	$class = $namespace .'\\Map\\'. $map . 'TableMap';
	
	if(!is_callable([$class, 'load']))
		fail(sprintf('TableMap %s can not be loaded [-s %s]', $class, $map));
	
	try {
		printf("Loading %s", $map);
		$start = microtime(true);
		
		$con = \Propel\Runtime\Propel::getWriteConnection($class::DATABASE_NAME);
		$class::load($con);
		
		printf("\t%.04fs.\n", (microtime(true) - $start) * 1000000);
	} catch (\Exception $e) {
		fail($e->getMessage());
	}
}