<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function devise_declarer_champs_extras($champs = array()){
	include_spip('base/devise_options');
	$lines = Array();
	foreach (devises_codes() as $code) {
		$lines[$code] = "$code - "._T("devise:$code");
	}
	$champs[] = new ChampExtra(array(
		'table' => 'auteurs', // sur quelle table ?
		'champ' => 'devise', // nom sql
		'label' => 'devise:devise_preferee', // chaine de langue 'prefix:cle' #TODO A localiser
		// 'precisions' => '', // precisions sur le champ
		'obligatoire' => false, // 'oui' ou '' (ou false)
		'rechercher' => false, // false, ou true ou directement la valeur de ponderation (de 1 à 8 generalement)
		'type' => 'menu-enum', // type de saisie
		'enum' => $lines, // valeurs possibles
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
	));
	return $champs;
}
?>
