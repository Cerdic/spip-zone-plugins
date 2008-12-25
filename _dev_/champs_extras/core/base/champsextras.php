<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function champsextras_declarer_tables_principales($tables_principales){
	// pouvoir utiliser la class ChampExtra
	include_spip('inc/champsextras');
	// recuperer les champs crees par les plugins
	$champs = pipeline('declarer_champs_extras', array());
	// ajouter les champs au tableau spip
	return declarer_champs_extras($champs, $tables_principales);
}
?>
