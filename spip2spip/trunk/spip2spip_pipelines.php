<?php
/**
 * Plugin Spip2spip
 * 
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


// les taches crons
function spip2spip_taches_generales_cron($taches_generales)
{
	//Recuperation de la configuration
	$conf = @unserialize($GLOBALS['meta']['spip2spip']);
	if (is_array($conf) and intval($conf['intervalle_cron']) > 1) {
		$taches_generales['spip2spip_syndic'] = 60 * intval($conf['intervalle_cron']);
	} else {
		$taches_generales['spip2spip_syndic'] = 60*5;  // tous les 5 min par defaut
	}
	$taches_generales['spip2spip_nettoyage'] = 60*60*24; // Une fois par jour
	return $taches_generales;
}

/**
 * Ajouter des éléments dans le header du privé.
 *
 * @param  string $flux
 * @return string
 */
function spip2spip_header_prive($flux)
{
	$page_exec = array('spip2spip', 'spip2spips');

	if (in_array(_request('exec'), $page_exec)) {
		$flux .= '<link rel="stylesheet" href="'
		. find_in_path('prive/themes/spip/style_prive_spip2spip.css')
		. '" type="text/css" media="all" />';
	}

	return $flux;
}

function spip2spip_affiche_milieu($flux) {
	$exec = $flux["args"]["exec"];

	if ($exec == "article") {
		$id_article = $flux["args"]["id_article"];
		$contexte = array('id_article'=>$id_article);
		$ret = recuperer_fond("prive/squelettes/inc/spip2spip_origine", $contexte);
		$flux["data"] .= $ret;
	}
 
	return $flux;
}

