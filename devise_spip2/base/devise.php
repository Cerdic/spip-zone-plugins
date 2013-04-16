<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function devise_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'auteurs', // sur quelle table ?
		'champ' => 'devise', // nom sql
		'label' => 'devise:devise_preferee', // chaine de langue 'prefix:cle' #TODO A localiser
		// 'precisions' => '', // precisions sur le champ
		'obligatoire' => false, // 'oui' ou '' (ou false)
		'rechercher' => false, // false, ou true ou directement la valeur de ponderation (de 1 à 8 generalement)
		'type' => 'devise', // type de saisie
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
		// experimental
		'saisie_externe' => true, // saisies
	));
	return $champs;
}
?>
