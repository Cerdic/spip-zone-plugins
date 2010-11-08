<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function bitcoin_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'auteurs',
		'champ' => 'adresse_bitcoin',
		'label' => 'bitcoin:adresse_bitcoin',
		'precisions' => 'bitcoin:adresse_bitcoin_precisions',
		'obligatoire' => false,
		'rechercher' => false,
		'type' => 'ligne',
		'sql' => "text NOT NULL DEFAULT ''",
	));
	return $champs;
}
?>
