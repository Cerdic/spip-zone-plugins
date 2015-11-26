<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function daterubriques_declarer_champs_extras($champs = array()){

	$champs[] = new ChampExtra(array(
		'table' => 'rubrique', // sur quelle table ?
		'champ' => 'date_utile', // nom sql
		'label' => 'daterubriques:date_label', // chaine de langue 'prefix:cle'
		'type' => 'date', // type de saisie
		'sql' => "datetime NOT NULL DEFAULT '".date("Y-m-d 00:00:00")."'", // declaration sql
		'saisie_externe' => true,
		'saisie_parametres' => array('defaut' => date("Y-m-d 00:00:00")), // Maintenant

	));
	return $champs;
}
?>
