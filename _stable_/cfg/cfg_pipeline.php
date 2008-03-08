<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Ajoute le bouton d'amin aux webmestres
function cfg_ajouter_onglets($flux) {
	if ($flux['args'] == 'configuration'
	AND autoriser('configurer')) {
		// on voit le bouton dans la barre "configurer"
		$flux['data']['cfg'] =
			new Bouton(
			_DIR_PLUGIN_CFG."cfg-22.png",  // icone
			_T('cfg:CFG'),	// titre
			generer_url_ecrire('cfg'),
			NULL,
			'cfg'
			);
	}
	return $flux;
}


/*
 * Gerer l'option <!-- head= xxx --> des fonds CFG
 * 
 * (pas sur que cela fonctionne avec #FORMULAIRE_CFG, 
 *  il faudra verifier)
 */
function cfg_insert_head($flux){
	// a voir
	return $flux;
}

function cfg_header_prive($flux){
	
	if (!_request('cfg') || (!_request('exec') == 'cfg')) {
		return $flux;
	}

	// Ajout des css de cfg (uniquement balise arbo pour l'instant) dans le header prive
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_CFG.'css/cfg.css" type="text/css" media="all" />';

	$cfg = cfg_charger_classe('cfg');
	include_spip('inc/filtres');
	$config = & new $cfg(
		($nom = sinon(_request('cfg'), '')),
		($vue = sinon(_request('cfg_vue'), $nom)),
		($cfg_id = sinon(_request('cfg_id'),''))
		);
	
	if ($config->head) 
		$flux .= "\n".$config->head;
	
	return $flux;
}
?>
