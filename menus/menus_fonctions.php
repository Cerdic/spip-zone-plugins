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

function menus_exposer($id_objet, $objet, $env, $on='on', $off=''){
	if (is_string($env))
		$env = unserialize($env);
	$primary = id_table_objet($objet);
	include_spip('public/quete');
	return calcul_exposer($id_objet, $primary, $env, '', $primary) ? $on : $off;
}
?>
