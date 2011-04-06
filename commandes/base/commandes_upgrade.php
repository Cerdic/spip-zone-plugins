<?php
/**
 * Plugin commandes pour Spip 2.1
 * Licence GPL
 * Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */

include_spip('inc/meta');
include_spip('base/create');

function commandes_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		maj_tables('spip_paniers');
		maj_tables('spip_paniers_liens');
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}

}


function commandes_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_paniers DROP COLUMN montant");
	sql_alter("TABLE spip_paniers DROP COLUMN reference");
	sql_alter("TABLE spip_paniers DROP COLUMN date_commande");
	sql_alter("TABLE spip_paniers DROP COLUMN date_paiement");
	sql_alter("TABLE spip_paniers_liens DROP COLUMN montant");
	sql_alter("TABLE spip_paniers_liens DROP COLUMN montant_taxe");
	sql_alter("TABLE spip_paniers_liens DROP COLUMN designation");
	effacer_meta($nom_meta_base_version);
}



?>
