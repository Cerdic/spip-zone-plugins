<?php
// =======================================================================================================================================
// Balise : #PLUGIN
// =======================================================================================================================================
// Auteur: Smellup
// Fonction : retourne une info d'un plugin donne
// =======================================================================================================================================
//
include_spip('inc/plugin');
function balise_PLUGIN($p) {

	$plugin = interprete_argument_balise(1,$p);
	if (!isset($plugin)) {
		$p->code = "''";
		$p->interdire_scripts = false;
	}
	else {
		$type_info = interprete_argument_balise(2,$p);
		if (!isset($type_info)) $type_info = 'est_actif';
		$p->code = 'calcul_info_plugin('.$plugin.', '.$type_info.')';
	}
	$p->statut = 'php';
	return $p;
}

function calcul_info_plugin($plugin, $type_info) {
	$dir_tous_plugins = array();
	$inf_tous_plugins = array();
	$plugins_valides = array();
	$plugins_actifs = array();
	$plugin_infos = array();
	$valeur_info = NULL;
	
	// Validite des parametres
	// - plugin : doit etre un prefixe valide de plugin installe
	// - info : si vide prend la valeur par defaut 'tout'
	$dir_tous_plugins = liste_plugin_files();
	$plugins_valides = liste_plugin_valides($dir_tous_plugins, $inf_tous_plugins);
	if (!array_key_exists(strtoupper($plugin), $plugins_valides)) 
		return $valeur_info;

	// Determination du plugin dans la liste des plugins valides. Si plusieurs plugins de meme prefixe on choisit dans l'ordre
	// - 1. Le plugin actif (il y en a forcement qu'un seul actif)
	// - 2. Le plugin inactif recense dans la liste des plugins valides
	$plugins_actifs = liste_plugin_actifs();
	if (isset($plugins_actifs[strtoupper($plugin)])) {
		$plugin_infos = $inf_tous_plugins[$plugins_actifs[strtoupper($plugin)]['dir']];
		$plugin_infos['est_actif'] = TRUE;
	}
	else {
		$plugin_infos = $inf_tous_plugins[$plugins_valides[strtoupper($plugin)]['dir']];
		$plugin_infos['est_actif'] = FALSE;
	}

	// Renvoi de l'information demandee
	switch (strtoupper($type_info)) {
	case 'NOM':
	    $valeur_info = propre($plugin_infos['nom']);
	    break;
	case 'VERSION':
	    $valeur_info = $plugin_infos['version'];
	    break;
	case 'AUTEUR':
	    $valeur_info = propre($plugin_infos['auteur']);
	    break;
	case 'ICON':
	    $valeur_info = $plugin_infos['icon'];
	    break;
	case 'DESCRIPTION':
	    $valeur_info = propre($plugin_infos['description']);
	    break;
	case 'LIEN':
		$valeur_info = formate_lien_plugin($plugin_infos['lien']);
	    break;
	case 'ETAT':
	    $valeur_info = formate_etat_plugin($plugin_infos['etat']);
	    break;
	case 'EST_ACTIF':
	    $valeur_info = $plugin_infos['est_actif'];
	    break;
	case 'TOUT':
	    $valeur_info = serialize($plugin_infos);
	    break;
}

	return $valeur_info;
}

function formate_lien_plugin($lien) {
	$ret = NULL;
	if (trim($lien)) {
		if (preg_match(',^https?://,iS', $lien))
			$ret = propre("[->".$lien."]");
		else
			$ret = propre($lien);
	}
	return $ret;
}

function formate_etat_plugin($etat) {
	$ret = NULL;
	if (!isset($etat))
		$etat = 'dev';
	switch ($etat) {
		case 'experimental':
			$ret = _T('plugin_etat_experimental');
			break;
		case 'test':
			$ret = _T('plugin_etat_test');
			break;
		case 'stable':
			$ret = _T('plugin_etat_stable');
			break;
		default:
			$ret = _T('plugin_etat_developpement');
			break;
	}
	return $ret;
}

?>