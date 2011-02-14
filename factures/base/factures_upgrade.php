<?php
/**
 * Plugin factures - Facturer avec Spip 2.0
 * Licence GPL (c) 2010
 * par Cyril Marion - Camille Lafitte
 */
 
include_spip('inc/meta');
include_spip('base/create');

function factures_upgrade($nom_meta_base_version, $version_cible){	
	/**
	 *
	 *  11/10/2010 : creation
	 *
	 */
	 
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}


	if (version_compare($current_version,"1.0.1","<")){
		maj_tables('spip_types_facture');
		sql_alter("TABLE spip_factures CHANGE id_type_document id_type_facture int(11) default NULL");
		ecrire_meta($nom_meta_base_version, $current_version="1.0.1");
	}

}

function factures_vider_tables($nom_meta_base_version) {
	
	sql_drop_table("spip_factures");
	sql_drop_table("spip_lignes_factures");
	sql_drop_table("spip_types_facture");

	effacer_meta($nom_meta_base_version);
}

?>
