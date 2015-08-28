<?php

require 'vendor/autoload.php';
require 'generated-conf/config.php'; // Propel's autoloader

function fail($message) {
	fwrite(STDERR, $message . PHP_EOL);
	exit(1);
}

$opt = getopt('n:s:');

if(!isset($opt['n']) && !isset($opt['s']))
	fail('Missing reqiured option');

$namespace = $opt['n'];
$maps = is_array($opt['s']) ? $opt['s'] : array($opt['s']);

foreach($maps as $map) {
	$class = $namespace .'\\'. $map;
	$classMap = $namespace .'\\Map\\'. $map . 'TableMap';
	
	if(!is_callable([$class, 'load']))
		fail(sprintf('TableMap %s can not be loaded [-s %s]', $class, $map));
	
	try {
		printf("Loading %s.\n", $map);
		$class::load();
	} catch (\Exception $e) {
		fail($e->getMessage());
	}
}