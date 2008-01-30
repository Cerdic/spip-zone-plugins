<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2008
 */

	$cejour= getdate();
	print_r($cejour);
	$centans = intval($cejour['year'])-100;
	echo "<p>$centans</p>";

?>