<?php
// =======================================================================================================================================
// Balise : #PLUGIN
// =======================================================================================================================================
// Auteur: SarkASmeL, James
// Fonction : retourne une info d'un plugin donne
// =======================================================================================================================================
//
include_spip('inc/plugin');

function calcul_info_plugin($plugin, $type_info) {
	$plugin = strtoupper($plugin);
	$type_info = strtolower($type_info);
	$plugins_actifs = liste_plugin_actifs();

	if(!$plugin)
		return serialize(array_keys($plugins_actifs));
	if(!empty($plugins_actifs[$plugin]))
		if($type_info == 'est_actif')
			return $plugins_actifs[$plugin] ? 1 : 0;
		else {
//			$dir_tous_plugins = liste_plugin_files();
//			$plugins_valides = liste_plugin_valides($dir_tous_plugins, $inf_tous_plugins);
			$plugins_valides = liste_plugin_valides(liste_plugin_files(), $inf_tous_plugins);
//			$plugin_infos = $inf_tous_plugins[$plugins_actifs[$plugin]['dir']];
		
			return $inf_tous_plugins[$plugins_actifs[$plugin]['dir']][$type_info];
//			return $plugin_infos[$type_info];
// 			return $plugins_actifs[$plugin][$type_info];
		}
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