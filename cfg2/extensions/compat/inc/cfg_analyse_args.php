<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2009, distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// surcharge de la fonction du core de CFG
// pour autoriser les raccourcis lire_config('~toto')
// DEPRECIE : declarer le depot systematiquement si different de meta ou metapack
// lire_config('extrapack::toto')
//
// en l'absence du nom de depot (gauche des ::) cette fonction prendra comme suit :
// ~ en premier caractere : tablepack
// : present avant un / : tablepack
// / present : metapack
// sinon meta
//
function inc_cfg_analyse_args($args) {
	list($depot, $args) = explode('::',$args,2);

	// si un seul argument, il faut trouver le depot
	if (!$args) {
		$args = $depot;
		if ($args[0] == '~'){
			$depot = 'tablepack';	
		} elseif (
			(list($head, $body) = explode('/',$args,2)) &&
			(strpos($head,':') !== false)) {
				$depot = 'tablepack';
		} else {
			if (strpos($args,'/') !== false)
				$depot = 'metapack';
			else 
				$depot = 'meta';
		}
	}
	
	return array($depot, $args);	
}

?>
