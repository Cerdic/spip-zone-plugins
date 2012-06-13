<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Page de param�trage du plugin
 *
 */

include_spip('inc/presentation');
include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');
include_spip('configuration/gmap_config_onglets');

if (!defined("_ECRIRE_INC_VERSION")) return;

// Bo�tes d'information gauche
function boite_info_help()
{
	$flux = '';
	
	// D�but de la bo�te d'information
	$flux .= debut_boite_info(true);
	
	// Info globale
	$flux .= propre(_T('gmap:info_configuration_gmap_prive'));
	
	// Lien sur l'aide
	$url = generer_url_ecrire('configurer_gmap_html').'&page=doc/parametrage#paramPrive';
	$flux .= propre('<a href="'.$url.'">'._T('gmap:info_configuration_help').'</a>');
	
	// Fin de la bo�te
	$flux .= fin_boite_info(true);
	
	return $flux;
}
function boite_info_important()
{
	$flux = '';

	if (gmap_est_actif())
	{
		// D�but de la bo�te d'information
		$flux .= debut_boite_info(true);
		
		// Affichage de l'API
		$api = gmap_lire_config('gmap_api', 'api', 'gma3');
		$apis = gmap_apis_connues();
		$api_desc = $apis[$api]['name'];
		$flux .= propre(_T('gmap:info_configuration_gmap_api').'<br />'.$api_desc);
		
		// Zones du site
		$bIsObject =
			(gmap_lire_config('gmap_objets_geo', 'type_rubriques', "oui") === 'oui') ||
			(gmap_lire_config('gmap_objets_geo', 'type_articles', "oui") === 'oui') ||
			(gmap_lire_config('gmap_objets_geo', 'type_documents', "oui") === 'oui') ||
			(gmap_lire_config('gmap_objets_geo', 'type_breves', "oui") === 'oui') ||
			(gmap_lire_config('gmap_objets_geo', 'type_mots', "oui") === 'oui') ||
			(gmap_lire_config('gmap_objets_geo', 'type_auteurs', "oui") === 'oui');
		if (!$bIsObject)
			$flux .= propre(_T('gmap:info_configuration_gmap_no_object'));
		$tout_le_site = (gmap_lire_config('gmap_objets_geo', 'tout_le_site', "oui") === 'oui') ? true : false;
		if ($tout_le_site)
			$flux .= propre(_T('gmap:info_configuration_gmap_site'));
		else
		{
			$simple_rubs = gmap_lire_config('gmap_objets_geo', 'liste', "");
			$flux .= propre(_T('gmap:info_configuration_gmap_rubriques'));
			$flux .= '<ul>'."\n";
			foreach ($simple_rubs as $rub)
			{
				$nom = sql_getfetsel('titre', 'spip_rubriques', 'id_rubrique=' . intval($rub));
				$flux .= '<li>'.$nom.'</li>'."\n";
			}
			$flux .= '</ul>'."\n";
		}
		
		// Fin de la bo�te
		$flux .= fin_boite_info(true);
	}
	else
	{
		$flux .= debut_boite_alerte();
		$flux .= propre(_T('gmap:alerte_gmap_inactif'));
		$flux .= propre(_T('gmap:alerte_gmap_inactif_ui'));
		$flux .= fin_boite_alerte();
	}
	
	return $flux;
}

// Page de configuration
function exec_configurer_gmap_prive_dist($class = null)
{
	// v�rifier une nouvelle fois les autorisations
	if (!autoriser('webmestre'))
	{
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	// Pipeline pour customiser
	pipeline('exec_init',array('args'=>array('exec'=>'configurer_gmap_prive'),'data'=>''));
	
	// affichages de SPIP
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('gmap:configuration_titre'), 'configurer_gmap', 'configurer_gmap_prive');
	echo "<br /><br /><br />\n";
	$logo = '<img src="'.find_in_path('images/logo-config-title-big.png').'" alt="" style="vertical-align: center" />';
	echo gros_titre(_T('gmap:configuration_titre'), $logo, false);
	echo barre_onglets("configurer_gmap", "cg_prive");
	echo debut_gauche('', true);
	
	// Informations sur la colonne gauche
	echo boite_info_help();
	echo boite_info_important();
	
	// Suite des affichages SPIP
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'configurer_gmap_prive'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'configurer_gmap_prive'),'data'=>''));
	echo debut_droite("", true);
	
	// Configuration des rubriques g�olocalisables
	$rubgeo = charger_fonction('rubgeo', 'configuration', true);
	if ($rubgeo)
		echo $rubgeo();

	// configuration des types de marqueurs et de leurs ic�nes
	$markers = charger_fonction('markers', 'configuration', true);
	if ($markers)
		echo $markers();
	
	// Configuration des valeurs par d�faut de l'interface (position par d�faut, types de cartes, contr�les...)
	$map_defaults = charger_fonction('map_defaults', 'configuration', true);
	if ($map_defaults)
		echo $map_defaults('configurer_gmap_prive', 'prive');
	
	// Configuration des rubriques g�olocalisables
	$eparams = charger_fonction('editparams', 'configuration', true);
	if ($eparams)
		echo $eparams();

	// pied de page SPIP
	echo fin_gauche() . fin_page();
}

?>
