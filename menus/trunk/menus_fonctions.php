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

/**
 * @param $tri
 * @param $quoi
 * @return string
 */
function menus_critere_tri($tri,$quoi){
	$tri = trim($tri);
	$inverse = ((strncmp($tri,"!",1)==0)?"!":"");
	if ($inverse)
		$tri = ltrim($tri,"!");
	$num = ((strncmp($tri,"num ",4)==0)?"num ":"");
	if ($num){
		$tri = trim(substr($tri,4));
	}

	// num
	if ($quoi=='num'){
		return $num?"$inverse$tri":"";
	}
	// alpha : on renvoie toujours le meme critre que num si num demande
	// num titre => {par num titre}{par titre}
	return "$inverse$tri";
}