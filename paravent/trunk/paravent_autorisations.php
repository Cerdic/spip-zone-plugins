<?php
/**
 * Plugin Paravent
 * (c) 2013 Scribe
 * Licence GNU/GPL
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;
 
// declaration vide pour ce pipeline.
function paravent_autoriser(){}
 
/**
 * Autoriser a voir le site en construction : par defaut tous les auteurs authentifies
 * @return booleen
 */
function autoriser_travaux(){
	if (isset($GLOBALS['visiteur_session']['statut'])) {
		$statuts = array('0minirezo','1comite');
		include_spip('inc/config');
		$visiteurs = lire_config('paravent/visiteurs');
		if (isset($visiteurs)) {
			$statuts[] = '6forum';
		}
		return in_array($GLOBALS['visiteur_session']['statut'], $statuts,true);
	}
}