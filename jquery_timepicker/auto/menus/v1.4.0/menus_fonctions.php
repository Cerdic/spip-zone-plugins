<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function menus_type_entree($nom){
	include_spip('inc/menus');
	$infos = menus_informer($nom);
	return $infos['nom'];
}

function menus_type_refuser_sous_menu($nom){
	include_spip('inc/menus');
	$infos = menus_informer($nom);
	return $infos['refuser_sous_menu'];
}

function menus_exposer($id_objet, $objet, $env, $on='on active', $off=''){
	if (is_string($env))
		$env = unserialize($env);
	$primary = id_table_objet($objet);
	include_spip('public/quete');
	return calcul_exposer($id_objet, $primary, $env, '', $primary) ? $on : $off;
}
?>
