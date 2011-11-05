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

// Choix de l'interface GIS
function gmap_formulaire_configuration_gis()
{
	$corps = "";
	
	// Texte explicatif
	$corps .= '
	<div class="texte"><p>'._T("gmap:configuration_gis_explic").'</p></div>';
	
	// Lire la configuration
	$apis = gmap_apis_connues();
	$apiSelected = gmap_lire_config('gmap_api', 'api', "gma3");

	// Paramétrage de l'API utilisée
	$corps .= '
		<div class="config_group">
			<select name="api_code" id="api_code" size="1">';
	foreach ($apis as $api => $infos)
	{
		$corps .= '
				<option value="'.$api.'"'.(!strcmp($apiSelected, $api) ? ' selected="selected"' : '').'>'.$infos['name'].'</option>';
	}
	$corps .= '
			</select>
			<p class="texte" id="api_code_desc"></p>
		</div>'."\n";

	// Script de mise à jour des explications
	$corps .= '<script type="text/javascript">'."\n".'	//<![CDATA['."\n";
	$corps .= '
function updateAPIDescription()
{
	var api = jQuery("#api_code").val();
	var desc = "";
	switch (api)
	{'."\n";
	foreach ($apis as $api => $infos)
	{
		$corps .= '
	case "'.$api.'" : desc = "'.$infos['explic'].'"; break;'."\n";
	}
	$corps .= '	default : desc = "'._T('gmap:gis_api_none_desc').'"; break;
	}
	jQuery("#api_code_desc").html(desc);
}
jQuery("#api_code").change(function() { updateAPIDescription(); });
jQuery(document).ready(function() { updateAPIDescription(); });
';
	$corps .= '	//]]>'."\n".'</script>'."\n";
		
	return gmap_formulaire_submit('configuration_gis', $corps, find_in_path('images/logo-config-gis.png'), _T('gmap:configuration_gis'));
}
function gmap_formulaire_configuration_gis_action()
{
	gmap_ecrire_config('gmap_api', 'api', _request('api_code'));
}

// Boîtes d'information gauche
function boite_info_help()
{
	$flux = '';
	
	// Début de la boîte d'information
	$flux .= debut_boite_info(true);
	
	// Info globale
	$flux .= propre(_T('gmap:info_configuration_gmap'));
	
	// Lien sur l'aide
	$url = generer_url_ecrire('configurer_gmap_html').'&page=doc/parametrage#paramSystem';
	$flux .= propre('<a href="'.$url.'">'._T('gmap:info_configuration_help').'</a>');
	
	// Fin de la boîte
	$flux .= fin_boite_info(true);
	
	return $flux;
}
function boite_info_important()
{
	$flux = '';

	if (gmap_est_actif())
	{
		// Début de la boîte d'information
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
		
		// Fin de la boîte
		$flux .= fin_boite_info(true);
	}
	else
	{
		$flux .= debut_boite_alerte();
		$flux .= propre(_T('gmap:alerte_gmap_inactif'));
		$flux .= fin_boite_alerte();
	}
	
	return $flux;
}


// Page de configuration
function exec_configurer_gmap_dist($class = null)
{
	// vérifier une nouvelle fois les autorisations
	if (!autoriser('webmestre'))
	{
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	// Traitements du formulaire post pour ce qui n'est pas en Ajax
	if (_request('config') == 'configuration_gis')
		gmap_formulaire_configuration_gis_action();
	$api = gmap_lire_config('gmap_api', 'api', "gma3");
	if (_request('config') == 'configuration_api')
	{
		$faire_api = charger_fonction('faire_api', 'configuration');
		$faire_api();
	}
	else if (_request('config') == 'configuration_rubgeo')
	{
		$faire_rubgeo = charger_fonction('faire_rubgeo', 'configuration');
		$faire_rubgeo();
	}
	
	// Pipeline pour customiser
	pipeline('exec_init',array('args'=>array('exec'=>'configurer_gmap'),'data'=>''));
	
	// Affichages de SPIP
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('gmap:configuration_titre'), 'configurer_gmap', 'configurer_gmap');
	echo "<br /><br /><br />\n";
	$logo = '<img src="'.find_in_path('images/logo-config-title-big.png').'" alt="" style="vertical-align: center" />';
	echo gros_titre(_T('gmap:configuration_titre'), $logo, false);
	echo barre_onglets("configurer_gmap", "cg_main");
	echo debut_gauche('', true);
	
	// Informations sur la colonne gauche
	echo boite_info_help();
	echo boite_info_important();
	
	// Suite des affichages SPIP
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'configurer_gmap'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'configurer_gmap'),'data'=>''));
	echo debut_droite("", true);
	
	// Configuration de l'API utilisée
	// Cette partie n'est pas en ajax, parce que les autres paramètres en dépendent
	echo gmap_formulaire_configuration_gis();
	
	// Selon l'API, autre paramétrages
	// Cette partie n'est pas en ajax, parce que les autres paramètres en dépendent
	$api_conf = charger_fonction('api', 'configuration', true);
	if ($api_conf)
		echo $api_conf();

	// Configuration des rubriques géolocalisables
	$rubgeo = charger_fonction('rubgeo', 'configuration', true);
	if ($rubgeo)
		echo $rubgeo();

	// configuration des types de marqueurs et de leurs icônes
	$markers = charger_fonction('markers', 'configuration', true);
	if ($markers)
		echo $markers();
	
	// Configuration des rubriques géolocalisables
	$eparams = charger_fonction('editparams', 'configuration', true);
	if ($eparams)
		echo $eparams();

	// pied de page SPIP
	echo fin_gauche() . fin_page();
}

?>
