<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function csv2spip_installation($num_version){
	// creer la table spip_tmp_csv2spip � l'install du plugin plut�t qu'� chaque utilisation du plugin
	include_spip('inc/csv2spip_tables');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();
	 
	// stocker le num de version dans spip_meta
	ecrire_meta('csv2spip_version',$num_version);

	if (count($Terreurs) != 0)
		echo implode('<br>',$Terreurs);

}
	
function csv2spip_desinstallation() {
	effacer_meta('csv2spip_version');
	ecrire_metas();
}

function csv2spip_upgrade($action){
// r�cup�rer le num�ro de version
  $Tplugins_actifs = liste_plugin_actifs();
  $version_script = $Tplugins_actifs['CSV2SPIP']['version'];

// install/d�sinstall ? 
	  switch ($action){
		case 'install':
			if (lire_meta('csv2spip_version') != $version_script)
				csv2spip_installation($version_script);
			break;
		case 'uninstall':
			csv2spip_desinstallation();
			break;
	}
}

?>
