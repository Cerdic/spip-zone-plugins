<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
*/

function abomailmans_ajouter_boutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees" 
	AND (!isset($GLOBALS['meta']['activer_abomailmans']) OR $GLOBALS['meta']['activer_abomailmans']!="non") ) {

	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu["abomailmans_tous"]= new Bouton(
		"../"._DIR_PLUGIN_ABOMAILMANS."/img_pack/mailman.gif",  // icone
		_T("abomailmans:abomailmans") //titre
		);
	}
	return $boutons_admin;
}


function abomailmans_header_prive($flux) {
		$exec = _request('exec');
		$flux .="\n\n<!-- PLUGIN ABOMAILMANS -->\n";
		$flux .= "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_ABOMAILMANS . "js/jQuery_myScripts.js\"></script>\n";
		if ($exec=="abomailmans_envoyer") {
			$flux .= "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_ABOMAILMANS . "js/datePicker.js\"></script>\n";
			$flux .= "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_ABOMAILMANS . "js/datePicker_myScripts.js\"></script>\n";
			$flux .= "<link rel=\"stylesheet\" href=\"" ._DIR_PLUGIN_ABOMAILMANS . "css/datePicker.css\" type=\"text/css\" />\n";}
		$flux .="<!-- / PLUGIN ABOMAILMANS -->\n\n";
		return $flux;
	}
function abomailmans_insert_head($flux) {
		$exec = _request('exec');
		$flux .="\n\n<!-- PLUGIN ABOMAILMANS -->\n";
		
			$flux .= "<link rel=\"stylesheet\" href=\"" ._DIR_PLUGIN_ABOMAILMANS . "css/abomailmans_style.css\" type=\"text/css\" />\n";
		$flux .="<!-- / PLUGIN ABOMAILMANS -->\n\n";
		return $flux;
	}
?>

