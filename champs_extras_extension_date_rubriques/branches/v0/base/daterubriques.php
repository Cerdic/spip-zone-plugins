<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function daterubriques_declarer_champs_extras($champs = array()){

	$champs[] = new ChampExtra(array(
		'table' => 'rubrique', // sur quelle table ?
		'champ' => 'ce_date', // nom sql
		'label' => 'daterubriques:date_label', // chaine de langue 'prefix:cle'
		'type' => 'date', // type de saisie
		'sql' => "date NOT NULL DEFAULT '".date("Y-m-d")."'", // declaration sql
		'saisie_externe' => true,
		'saisie_parametres' => array(
										'defaut' => date("Y-m-d")), // Maintenant

	));
	return $champs;
}
?>
