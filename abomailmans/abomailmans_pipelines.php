<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * $Id: abomailmans_interface.php 31752 2009-09-23 00:09:48Z kent1@arscenic.info $
*/
 
function abomailmans_autoriser(){}

// acces aux listes abomailmans = tous les admins
function autoriser_abomailmans_dist($faire, $type, $id, $qui, $opt) {
	return (($GLOBALS['meta']["activer_abomailmans"] != 'non')
			AND ($qui['statut'] == '0minirezo') 
			AND !$qui['restreint']
			);
}
// autorisation des boutons
function autoriser_abomailmans_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', $type, $id, $qui, $opt);
}

function autoriser_abomailmans_creer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('modifier', $type, $id, $qui, $opt);
}

function autoriser_abomailmans_modifier_dist($faire, $type, $id, $qui, $opt){
	return ($qui['statut']=='0minirezo')  AND !$qui['restreint'];
}


function abomailmans_header_prive($flux) {
	$exec = _request('exec');
	$flux .="\n\n<!-- PLUGIN ABOMAILMANS -->\n";
	if ($exec=="abomailmans_envoyer") {
		$flux .= "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_ABOMAILMANS . "js/datePicker.js\"></script>\n";
		$flux .= "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_ABOMAILMANS . "js/datePicker_myScripts.js\"></script>\n";
		$flux .= "<link rel=\"stylesheet\" href=\"" ._DIR_PLUGIN_ABOMAILMANS . "js/datePicker.css\" type=\"text/css\" />\n";}
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
	//$taches_generales['abomailmans_envois'] = 60 * 10; // toutes les 10 minutes
	return $taches_generales;
}


// Initialise les reglages sous forme de tableau
function abomailmans_go($x) {
	if (!is_array($GLOBALS['abomailmans']	= @unserialize($GLOBALS['meta']['abomailmans'])))
		$GLOBALS['abomailmans'] = array();
	return $x;
}

?>