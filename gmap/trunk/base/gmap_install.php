<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 */

include_spip('inc/meta');
include_spip('base/abstract_sql');
include_spip('inc/gmap_db_utils');
include_spip('inc/gmap_config_utils');

// Versions correspondant � ce code
$GLOBALS['gmap_version'] = 0.1;
$GLOBALS['gmap_base_version'] = 0.1;

// Initialisation de la configuration
function gmap_initialize_configuration()
{
	// Api
	gmap_init_config('gmap_api', 'api', 'gma3');
		
	// Initialiser le param�trage par d�faut de l'API
	$iniAPI = charger_fonction('init_api', 'configuration');
	if ($iniAPI)
		$iniAPI();
	
	// Initialiser les zones autoris�es
	$iniRUB = charger_fonction('init_rubgeo', 'configuration');
	if ($iniRUB)
		$iniRUB();
		
	// Initialiser l'interface dans toutes les APIs
	$iniUI = charger_fonction('init_map_defaults', 'configuration');
	if ($iniUI)
		$iniUI();
	$iniMarkersUI = charger_fonction('init_markers_behavior', 'configuration');
	if ($iniMarkersUI)
		$iniMarkersUI();
}

// Mise � jour ou installation de la base de donn�es	
function gmap_upgrade($nom_meta_base_version, $version_cible)
{
    $current_version = 0.0;

    // Recherche de la version de la base de donn�es
    if ((!isset($GLOBALS['meta'][$nom_meta_base_version])) ||
        (($current_version = $GLOBALS['meta'][$nom_meta_base_version]) != $version_cible))
    {
        // Cr�ation de la base de donn�es 'from scratch'
		if (version_compare($current_version,'0.0','<='))
		{
    		// Laisser SPIP cr�er la base gr�ce aux tables d�clar�es par les pipelines
			include_spip('base/create');
			include_spip('base/abstract_sql');
            creer_base();
			
			// Initialiser la base avec les types par d�faut
			gmap_cree_types_defaut();
        }
		
		// Passage en 1.0 pour la publication du plugin, mais pas de modif...
		if (version_compare($current_version,'1.0','<'))
		{
			// Donc rien � faire.
		}

		// Stocker la version, quoiqu'il arrive : on a d� mettre � jour
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	
    // Ajouter les formats KML et KMZ dans les formats uploadables...
	gmap_verif_types_documents();
	
	// Initialiser les param�tres pour que �a puisse tourner sans besoin
	// de param�trage
	gmap_initialize_configuration();

    // R��crire tous les param�tres
    ecrire_metas();
}

// Suppression des tables
function gmap_vider_tables($nom_meta_base_version)
{
    sql_drop_table("spip_gmap_points");
    sql_drop_table("spip_gmap_points_liens");
    sql_drop_table("spip_gmap_types");
    sql_drop_table("spip_gmap_labels");

    effacer_meta($nom_meta_base_version);
	effacer_meta("gmap_version");
	
    ecrire_metas();
}

?>
