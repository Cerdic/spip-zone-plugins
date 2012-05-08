<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
//error_reporting(E_ALL); ini_set('display_errors',1);

function count_euDock($incremente=false){
	static $euDock_count=0;
	if($incremente)
		$euDock_count++;
//	echo "<p>euDock COUNT => ".$euDock_count."</p>";
	return $euDock_count;
}

?>