<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function devise_declarer_champs_extras($champs = array()){
	$champs['spip_auteurs']['devise'] = array(
		'saisie' => 'devise',
		'options' => array(
			'nom' => 'devise',
			'label' => _T('devise:devise_preferee'),
			'sql' => "text NOT NULL DEFAULT ''", // declaration sql
			'rechercher' => false, // false, ou true ou directement la valeur de ponderation (de 1 à 8 generalement)
			'defaut' => '',
			'type' => 'devise', // type de saisie
			'versionner' => true,
		),
	);
	return $champs;
}
?>
