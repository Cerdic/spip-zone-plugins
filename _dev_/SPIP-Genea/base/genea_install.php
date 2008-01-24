<?php
/* *********************************************************************
   *
   * Copyright (c) 2007-2008
   * Xavier Burot
   * fichier : base/genea_install.php
   *
   * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
   *
   *********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return; // Securite

if (!function_exists("spip_query")) include_ecrire("inc/utils");

include_spip('base/genea_base');

// Creation et mise a jour des tables necessaires a SPIP-GENEA
function genea_upgrade_tables($version_installee) {
	include_spip('base/create');  // definir la fonction
	creer_base();
	echo "<p><strong>"._T('genea:nom_plugin')." :</strong><br />"._T('genea:install_ok')."</p>";
	ecrire_meta('genea_version', $GLOBALS[version_base_genea]);
	ecrire_metas();
	return true;
}

function genea_efface_tables(){
	global $tables_genea;
	reset($tables_genea);
	$erreur="";
	while (list($key, $val) = each($tables_genea)) {
		spip_query("DROP TABLE IF EXISTS $val ");
		if (mysql_errno()) $erreur.= "<li>$val (" . mysql_errno() . ")</li>";
	}
	if ($erreur!='') {
		echo "<p><strong>"._T('genea:nom_plugin')." :</strong><br />"._T('genea:desinstall_err')."<ul>".$erreur."</ul>"."</p>";
		die();
		return false;
	}else{
//		echo "<p><strong>"._T('genea:nom_plugin')." :</strong><br />"._T('genea:desinstall_ok')."</p>";
		effacer_meta('genea_version');
		ecrire_metas();
		return true;
	}
}

function genea_install($action){

	switch ($action) {  
	
		// Test si les tables ont deja ete installees et elles sont de la bonne version
		case 'test':
			return (isset($GLOBALS['meta']['genea_version']) AND version_compare($GLOBALS['meta']['genea_version'],$GLOBALS['version_base_genea'],'='));
			break;

		// Si les tables necessaires n'existent pas, elles sont crees dans la base
		case 'install':
			return genea_upgrade_tables($GLOBALS['meta']['genea_version']);
			break;

		// Permet la suppression propre des tables dans la base
		case 'uninstall' :
			return genea_efface_tables();
			break;
		default :
	}
}

/* rend disponible l'icone de désinstallation */
/* ca doit certainement permettre d'effacer les fichiers et autres */
function genea_uninstall(){
}
?>