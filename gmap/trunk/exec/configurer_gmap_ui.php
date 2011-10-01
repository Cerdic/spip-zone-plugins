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

// Page de configuration
function exec_configurer_gmap_ui_dist($class = null)
{
	// vérifier une nouvelle fois les autorisations
	if (!autoriser('webmestre'))
	{
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	// Pipeline pour customiser
	pipeline('exec_init',array('args'=>array('exec'=>'configurer_gmap_ui'),'data'=>''));
	
	// affichages de SPIP
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('gmap:configuration_titre'), 'configurer_gmap', 'configurer_gmap_ui');
	echo "<br /><br /><br />\n";
	$logo = '<img src="'.find_in_path('images/logo-config-title-big.png').'" alt="" style="vertical-align: center" />';
	echo gros_titre(_T('gmap:configuration_titre'), $logo, false);
	echo barre_onglets("configurer_gmap", "cg_ui");
	echo debut_gauche('', true);
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'configurer_gmap_ui'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'configurer_gmap_ui'),'data'=>''));
	echo debut_droite("", true);
	
	// Configuration des valeurs par défaut de l'interface (position par défaut, types de cartes, contrôles...)
	$map_defaults = charger_fonction('map_defaults', 'configuration');
	if ($map_defaults)
		echo $map_defaults();
	
	// Paramétrage du comportement des marqueurs : info-bulles, regroupements...
	$markers_behavior = charger_fonction('markers_behavior', 'configuration');
	if ($markers_behavior)
		echo $markers_behavior();
	
	// configuration des types de marqueurs et de leurs icônes
	$markers = charger_fonction('markers', 'configuration');
	if ($markers)
		echo $markers();

	// pied de page SPIP
	echo fin_gauche() . fin_page();
}

?>
