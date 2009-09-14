<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function menus_type_entree($nom){
	include_spip('inc/menus');
	$dispo = menus_lister_disponibles();
	return $dispo[$nom]['nom'];
}

function menus_type_refuser_sous_menu($nom){
	include_spip('inc/menus');
	$dispo = menus_lister_disponibles();
	return $dispo[$nom]['refuser_sous_menu'];
}

?>
