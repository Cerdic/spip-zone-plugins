<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Outils de présentation : gestion des onglets de la config
 *
 */

include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');

// Définition des onglets de la page de configuration
function barre_onglets_configurer_gmap()
{
	$onglets = array();
	$onglets['cg_main']=
		  new Bouton(find_in_path('images/logo-config-gmap-main.png'), 'gmap:onglet_config_principale',
			generer_url_ecrire("configurer_gmap"));
	$onglets['cg_ui']=
		  new Bouton(find_in_path('images/logo-config-gmap-ui.png'), 'gmap:onglet_config_interface',
			generer_url_ecrire("configurer_gmap_ui"));
	$onglets['cg_import']=
		  new Bouton(find_in_path('images/logo-config-gmap-import.png'), 'gmap:onglet_config_imports',
			generer_url_ecrire("configurer_gmap_import"));
	$onglets['cg_help']=
		  new Bouton(find_in_path('images/logo-config-gmap-help.png'), 'gmap:onglet_config_help',
			parametre_url(generer_url_ecrire("configurer_gmap_html"), "page", "doc/index", "&"));
	/* Supprimé : pas paticulièrement de crédits à exprimer...
	$onglets['cg_credits']=
		  new Bouton(find_in_path('images/logo-config-gmap-credits.png'), 'gmap:onglet_config_credits',
			parametre_url(generer_url_ecrire("configurer_gmap_html"), "page", "credits/index", "&"));*/
	return $onglets;
}


?>