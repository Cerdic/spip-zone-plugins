<?php

function formulaires_feedburner_charger_dist ($feedId='') {
	if ($feedId=='') {
		$feedId=lire_config ('feedburner/feedId');
	}
	$valeurs=array('email'=>'','_feedId'=>$feedId);
	
	return $valeurs;
}

?>