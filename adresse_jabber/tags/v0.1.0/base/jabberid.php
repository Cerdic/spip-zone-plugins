<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function jabberid_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'auteur',
		'champ' => 'jid', // nom sql
		'label' => 'jabberid:adresse_jabber', // chaine de langue 'prefix:cle'
		'precisions' => 'jabberid:adresse_jabber_precisions',
		'type' => 'ligne', // type de saisie
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
	));
	return $champs;
}
?>
