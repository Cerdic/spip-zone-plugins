<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Page de paramétrage du plugin
 *
 */

include_spip('inc/presentation');
include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');
include_spip('configuration/gmap_config_onglets');

if (!defined("_ECRIRE_INC_VERSION")) return;

// Boîtes d'information gauche
function boite_info_help()
{
	$flux = '';
	
	// Début de la boîte d'information
	$flux .= debut_boite_info(true);
	
	// Info globale
	$flux .= propre(_T('gmap:info_configuration_gmap_import'));
	
	// Lien sur l'aide
	$url = generer_url_ecrire('configurer_gmap_html').'&page=doc/parametrage#paramImportExport';
	$flux .= propre('<a href="'.$url.'">'._T('gmap:info_configuration_help').'</a>');
	
	// Fin de la boîte
	$flux .= fin_boite_info(true);
	
	return $flux;
}

// Page de configuration
function exec_configurer_gmap_import_dist($class = null)
{
	// vérifier une nouvelle fois les autorisations
	if (!autoriser('webmestre'))
	{
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	// Pipeline pour customiser
	pipeline('exec_init',array('args'=>array('exec'=>'configurer_gmap_import'),'data'=>''));
	
	// affichages de SPIP
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('gmap:configuration_titre'), 'configurer_gmap', 'configurer_gmap_import');
	echo "<br /><br /><br />\n";
	$logo = '<img src="'.find_in_path('images/logo-config-title-big.png').'" alt="" style="vertical-align: center" />';
	echo gros_titre(_T('gmap:configuration_titre'), $logo, false);
	echo barre_onglets("configurer_gmap", "cg_import");
	echo debut_gauche('', true);
	
	// Informations sur la colonne gauche
	echo boite_info_help();
	
	// Suite des affichages SPIP
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'configurer_gmap_import'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'configurer_gmap_import'),'data'=>''));
	echo debut_droite("", true);
	
	// Après installation, récupération des données de gis/geomap
	$import = charger_fonction('import', 'configuration');
	echo $import();
	
	// pied de page SPIP
	echo fin_gauche() . fin_page();
}

?>
