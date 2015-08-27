<?php
namespace Propel\Generator\Behavior\Store;

/**
 * Interface for objects which can be stored in file
 * to be pushed later in a batch
 * 
 * @author Maurice Prosper <maurice.prosper@ttu.edu>
 */
interface Storable {
	/**
	 * Data that will be stored in a file
	 * @return mixed
	 */
	public function escape();
}
