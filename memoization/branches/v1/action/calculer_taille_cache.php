<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2012                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;


include_once _DIR_RESTREINT."action/calculer_taille_cache.php";

/**
 * Calculer la taille du cache ou du cache image pour l'afficher en ajax sur la page d'admin de SPIP
 * pour le cache principal de SPIP, tenir compte de la methode de memoization utilisee
 *
 * @param string|null $arg
 */
function action_calculer_taille_cache($arg=null){
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	include_spip('inc/filtres');

	if ($arg=='images'){
		return action_calculer_taille_cache_dist($arg);
	}
	else {
		// deleguer a memoization
		include_spip("inc/memoization");
		global $Memoization;
		if (!is_null($taille=$Memoization->size())
		  AND $taille!==false){
			$res = ($taille<=50000) ?
				_T('taille_cache_vide')
				:
				_T('taille_cache_octets',array('octets'=>taille_en_octets($taille)));
			$res = "<b>$res</b>";
		}
		else {
			$res = _T('memoization:info_taille_cache_inconnue',array('methode'=>"<b>".$Memoization->methode."</b>"));
		}
	}
	
	$res = "<p>$res</p>";
	ajax_retour($res);
}



?>