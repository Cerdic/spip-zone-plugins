<?php
/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2009, distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/*
 *  Ajoute le bouton d'amin aux webmestres
 */
function cfg_compat_ajouter_boutons($flux) {
	if (autoriser('configurer','cfg')) {
		$menu = "configuration";
		$icone = "images/cfg-22.png";
		if (isset($flux['bando_configuration'])){
			$menu = "bando_configuration";
			$icone = "images/cfg-16.png";
		}
		// on voit le bouton dans la barre "configuration"
		$flux[$menu]->sousmenu['cfg']= new Bouton(
		_DIR_PLUGIN_CFG.$icone,  // icone
		_T('cfg:CFG'));
	}
	return $flux;
}


/*
 * - Gerer l'option <!-- head= xxx --> (/!\ DEPRECIE)
 *   des fonds CFG uniquement dans le prive,
 * - ajouter la css pour cfg_arbo
 *
 */
function cfg_compat_header_prive($flux){

	if (!_request('cfg') || (!_request('exec') == 'cfg')) {
		return $flux;
	}

	// Ajout des css de cfg (uniquement balise arbo pour l'instant) dans le header prive
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_CFG_COMPAT.'css/cfg.css" type="text/css" media="all" />';

	include_spip('inc/filtres');
	include_spip('inc/cfg_formulaire');
	$config = new cfg_formulaire(
				sinon(_request('cfg'), ''),
				sinon(_request('cfg_id'),''));

	if ($config->param['head'])
		$flux .= "\n".$config->param['head'];

	return $flux;
}

?>
