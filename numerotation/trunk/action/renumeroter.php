<?php
/*
 * Plugin numero
 * aide a la numerotation/classement des objets dans l'espace prive
 *
 * Auteurs :
 * Cedric Morin, Nursit.com
 * (c) 2008-2014 - Distribue sous licence GNU/GPL
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Numeroter les objets d'un type et parent donnes
 * arg au format type-id
 */
function action_renumeroter_dist($arg = null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$arg = explode('-',$arg);
	$type = 'rubrique';
	if (preg_match(',^\w*$,',$arg[0]))
		$type = $arg[0];
	
	include_spip('inc/numeroter');
	numero_numeroter_objets($type,intval($arg[1]));
}

?>