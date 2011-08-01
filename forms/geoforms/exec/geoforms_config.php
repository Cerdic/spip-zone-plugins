<?php
/*
 * GeoForms
 * Geolocalistion dans les tables et les formulaires
 *
 * Auteur :
 * Cedric Morin
 * (Modifié par Carl V.)
 *
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip("inc/utils");
include_spip("inc/presentation");

function exec_geoforms_config(){

	global $connect_statut,$spip_lang_right;
	
	
	/**** Modification pour être compatible avec SPIP 2 ****/
	/** (cf. --> http://www.spip-contrib.net/PortageV2-Migrer-un-plugin-vers-SPIP2 ) **/
	
	// Test de la version de SPIP 
	if (version_compare($GLOBALS['spip_version_code'],'1.9300','<')) // SPIP <= 1.9.2x
	{ 
		debut_page(_T('geoforms:configuration'));
		
	} elseif (version_compare($GLOBALS['spip_version_code'],'2','>=')) // SPIP >= 2.x
	{ 
		$commencer_page = charger_fonction('commencer_page', 'inc');
	
		// Affichage d'un bouton 'GeoForms' situé dans le sous-menu de 'Configuration' (espace privé)
		// permettant de paramétrer GeoForms (MAIS pour l'instant ne marche pas!!)
		echo $commencer_page( _T("geoforms:configuration") );
	};
	
	/*************************************************************/
	

	// Configuration du systeme geographique
	echo debut_grand_cadre(true);
	
	
	echo "<div>";
	
	// Titre de la page de configuration
	echo "<h3>";
	echo _T("geoforms:configuration_titre") . " : ";
	echo "</h3>";
	
	// 'Warning' à afficher sur la page de configuration SI SPIP >= 2.1
	// (à tester sur d'autres versions de SPIP...) 
	if ( version_compare($GLOBALS['spip_version_code'],'2.1','>=') ) 
	{
		echo "<p>";
		echo _T("geoforms:configuration_warning");
		echo "<br />";
		echo _T("geoforms:configuration_warning2");
		echo "</p>";
	}
		
	echo "</div>";
	
	echo "<br />";	
	
	if( autoriser('administrer','geoforms') )
	{
		/* La fonction 'inc_geomap_config()' appelée est définie 
		dans "\plugins\googlemap_api\inc\geomap_config.php" */
		$geomap_config = charger_fonction('geomap_config','inc');
		
		echo $geomap_config();
	}
	
	echo fin_grand_cadre(true);
	
	fin_page();
	
}

?>