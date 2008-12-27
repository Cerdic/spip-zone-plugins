<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function iextra_declarer_champs_extras($champs=array()) {
	include_spip('inc/iextra');
	$extras = iextra_get_extras();
	foreach($extras as $e) {
		$champs[] = new ChampExtra($e);
	}
	return $champs;
}
?>
