<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * $Id: abomailmans_interface.php 31752 2009-09-23 00:09:48Z kent1@arscenic.info $
*/

function abomailmans_ajouter_boutons($flux) {
	if (isset($flux['bando_edition'])){
		return $flux;
	}
	global $visiteur_session;
	// si on est admin
	if ($visiteur_session['statut'] == "0minirezo" && !$visiteur_session['restreint']
	AND (!isset($GLOBALS['meta']['activer_abomailmans']) OR $GLOBALS['meta']['activer_abomailmans']!="non") ) {
		$menu = 'naviguer';
		$icone = find_in_path("/img_pack/mailman.gif");
		$flux[$menu]->sousmenu['abomailmans_tous'] = new Bouton(
			$icone,  // icone
			_T("abomailmans:bouton_listes_diffusion")
		);
	}
	return $flux;
}

function abomailmans_header_prive($flux) {
	$exec = _request('exec');
	$flux .="\n\n<!-- PLUGIN ABOMAILMANS -->\n";
	if ($exec=="abomailmans_envoyer") {
		$flux .= "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_ABOMAILMANS . "js/datePicker.js\"></script>\n";
		$flux .= "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_ABOMAILMANS . "js/datePicker_myScripts.js\"></script>\n";
		$flux .= "<link rel=\"stylesheet\" href=\"" ._DIR_PLUGIN_ABOMAILMANS . "css/datePicker.css\" type=\"text/css\" />\n";}
	$flux .="<!-- / PLUGIN ABOMAILMANS -->\n\n";
	return $flux;
}

/**
 * 
 * Declarer la tache cron de abomailman lente (messagerie de l'espace prive)
 * @param array $taches_generales
 * @return array 
 */
function abomailmans_taches_generales_cron($taches_generales){
	$taches_generales['abomailmans'] = 60 * 10; // toutes les 10 minutes
	return $taches_generales;
}


// Initialise les reglages sous forme de tableau
function abomailmans_go($x) {
	if (!is_array($GLOBALS['abomailmans']	= @unserialize($GLOBALS['meta']['abomailmans'])))
		$GLOBALS['abomailmans'] = array();
	return $x;
}

?>