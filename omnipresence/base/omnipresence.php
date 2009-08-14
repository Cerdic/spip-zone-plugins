<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function omnipresence_declarer_champs_extras($champs = array()){
	include_spip('inc/omnipresence');
	$champs[] = new ChampExtra(array(
		'table' => 'auteurs', // sur quelle table ?
		'champ' => CHAMP_SERVEUR_OMNIPRESENCE, // nom sql
		'label' => 'omnipresence:serveur_omnipresence_nom_champ', // chaine de langue 'prefix:cle' #TODO A localiser
		'precisions' => 'omnipresence:serveur_omnipresence_precisions', // precisions sur le champ
		'obligatoire' => false, // 'oui' ou '' (ou false)
		'rechercher' => false, // false, ou true ou directement la valeur de ponderation (de 1 à 8 generalement)
		'type' => 'ligne', // type de saisie
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
	));
	return $champs;
}
?>
