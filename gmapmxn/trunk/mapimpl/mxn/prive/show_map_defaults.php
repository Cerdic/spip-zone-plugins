<?php
/*
 * Extension Mapstraction pour GMap
 *
 * Auteur : Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');
include_spip('inc/provider_caps');

// �l�ments de l'interface
function _get_mxn_ui_elements($profile = 'interface')
{
	$corps = "";
	
	// Clefs d'acc�s aux valeurs enregistr�es
	if (!isset($profile))
		$profile = 'interface';
	$apiConfigKey = 'gmap_mxn_'.$profile;

	// Avertissements
	$controls = '';
	$msg = _T('gmapmxn:controls_special_info_'.gmapmxn_getProvider());
	if ($msg !== 'nop')
		$controls .= '
	<p>'.$msg.'</p>';
	if (!gmapmxn_hasCapability('auto_updt_controls'))
		$controls .= '
	<p class="warning">'._T('gmapmxn:controls_no_update').'</p>';
	if (strlen($controls))
		$corps .= '
	<div class="padding"><div class="interior">'.$controls.'
	</div></div>' . "\n";

	// Fonds de cartes
	$controls = '';
	if (gmapmxn_hasCapability('maptypes')) // peut-on choisir ?
	{
		$default_type = gmap_lire_config($apiConfigKey, 'type_defaut', "mixte");
		$controls .= '
		<label for="type_carte_defaut">'._T('gmap:explication_type_carte_defaut').'</label>
		<select name="type_carte_defaut" id="type_carte_defaut" class="tracked">
			<option value="plan"'.(($default_type === "plan")?' selected="selected"':'').'>'._T('gmap:type_carte_plan').'</option>
			<option value="satellite"'.(($default_type === "satellite")?' selected="selected"':'').'>'._T('gmap:type_carte_satellite').'</option>
			<option value="mixte"'.(($default_type === "mixte")?' selected="selected"':'').'>'._T('gmap:type_carte_mixte').'</option>
			<option value="physic"'.(($default_type === "physic")?' selected="selected"':'').'>'._T('gmap:type_carte_physic').'</option>
		</select>';
	}
	if (gmapmxn_hasCapability('ctrl_maptypes'))
	{
		$types_control = gmap_lire_config($apiConfigKey, 'types_control', "oui");
		$controls .= '
			<p><input type="checkbox" name="types_control" id="types_control"'.$tracked.' value="oui"'.(($types_control==="oui")?'checked="checked"':'').' />&nbsp;<label for="types_control">'._T('gmapmxn:choix_types_control').'</label></p>';
	}
	if (strlen($controls))
	{
		$corps .= '
<fieldset id="config_carte_fonds" class="config_group">
	<legend>'._T('gmap:configuration_defaults_types').'</legend>
	<div class="padding"><div class="interior">'.$controls.'
	</div></div>
</fieldset>' . "\n";
	}
	
	// Choix du type de contr�les
	$controls = '';
	$tracked = gmapmxn_hasCapability('auto_updt_controls') ? ' class="tracked"' : '';
	if (gmapmxn_hasCapability('ctrl_zoom'))
	{
		$zoom_control = gmap_lire_config($apiConfigKey, 'zoom_control', "small");
		$controls .= '
			<p><label for="zoom_control">'._T('gmapmxn:choix_zoom_control').'</label>&nbsp;<select name="zoom_control" id="zoom_control"'.$tracked.'>
				<option value="none"'.(($zoom_control === "none")?' selected="selected"':'').'>'._T('gmapmxn:choix_zoom_control_none').'</option>
				<option value="small"'.(($zoom_control === "small")?' selected="selected"':'').'>'._T('gmapmxn:choix_zoom_control_small').'</option>
				<option value="large"'.(($zoom_control === "large")?' selected="selected"':'').'>'._T('gmapmxn:choix_zoom_control_large').'</option>
			</select></p>';
	}
	if (gmapmxn_hasCapability('ctrl_pan'))
	{
		$pan_control = gmap_lire_config($apiConfigKey, 'pan_control', "oui");
		$controls .= '
			<p><input type="checkbox" name="pan_control" id="pan_control"'.$tracked.' value="oui"'.(($pan_control==="oui")?'checked="checked"':'').' />&nbsp;<label for="pan_control">'._T('gmapmxn:choix_pan_control').'</label></p>';
	}
	if (gmapmxn_hasCapability('ctrl_scale'))
	{
		$scale_control = gmap_lire_config($apiConfigKey, 'scale_control', "oui");
		$controls .= '
			<p><input type="checkbox" name="scale_control" id="scale_control"'.$tracked.' value="oui"'.(($scale_control==="oui")?'checked="checked"':'').' />&nbsp;<label for="scale_control">'._T('gmapmxn:choix_scale_control').'</label></p>';
	}
	if (gmapmxn_hasCapability('ctrl_overview'))
	{
		$overview_control = gmap_lire_config($apiConfigKey, 'overview_control', "non");
		$controls .= '
			<p><input type="checkbox" name="overview_control" id="overview_control"'.$tracked.' value="oui"'.(($overview_control==="oui")?'checked="checked"':'').' />&nbsp;<label for="overview_control">'._T('gmapmxn:choix_overview_control').'</label></p>';
	}
	if (strlen($controls))
	{
		$corps .= '
<fieldset id="config_carte_params" class="config_group">
	<legend>'._T('gmap:configuration_defaults_controls').'</legend>
	<div class="padding"><div class="interior">'.$controls.'
	</div></div>
</fieldset>' . "\n";
	}
	
	// Logo
	$corps .= '
<p style="height: 10px; line-height: 10px; border: 0; padding: 0; margin: 0"><img src="'._DIR_PLUGIN_GMAPMXN.'/images/poweredby.png" alt="Powered by Mapstraction" /></p>';
	
	return $corps;
}

// Fonction qui lit les param�tres de la carte depuis l'interface ci-dessus
function _get_mxn_get_params()
{
	// Script sp�cifique pour lire le param�trage
	$provider = gmapmxn_getProvider();
	$providerCaps = gmapmxn_getProviderCaps($provider);
	$getParams = '
// Lire les param�tres de la carte dans les �l�ments de formulaire
function getParams(bIncludeViewport)
{
	var params = new Object();
	
	// Provider
	params["provider"] = "'.$provider.'";
';

	// Passer toutes les possibilit�s des providers (�a �vite de les ajouter chaque fois...)
	$getParams .= '
	params["caps"] = {';
	foreach ($providerCaps as $name => $value)
	$getParams .= '
		'.$name.': '.(($value === 'oui') ? 'true' : 'false').',';
	$getParams .= '
		};';

	if ($providerCaps['maptypes'] === 'oui')
	{
		$getParams .= '
	// Fonds de carte
	params["maptypes"] = true;
	params["map_type"] = jQuery("#type_carte_defaut").val();
';
	}
	else
	{
		$getParams .= '
	// Pas de fond de carte
	params["maptypes"] = false;
';
	}
	
	$getParams .= '
	// Contr�les
	params["ctrl_pan"] = '.(gmapmxn_hasCapability('ctrl_pan') ? '(jQuery("#pan_control:checked").val() == "oui") ? true : false' : 'false').';
	params["ctrl_zoom"] = '.(gmapmxn_hasCapability('ctrl_zoom') ? '(jQuery("#zoom_control").val() == "none") ? false : jQuery("#zoom_control").val()' : 'false').';
	params["ctrl_scale"] = '.(gmapmxn_hasCapability('ctrl_scale') ? '(jQuery("#scale_control:checked").val() == "oui") ? true : false' : 'false').';
	params["ctrl_overview"] = '.(gmapmxn_hasCapability('ctrl_overview') ? '(jQuery("#overview_control:checked").val() == "oui") ? true : false' : 'false').';
	params["ctrl_map_type"] = '.(gmapmxn_hasCapability('ctrl_maptypes') ? '(jQuery("#types_control:checked").val() == "oui") ? true : false;' : 'false').';';
	
	$getParams .= '	
	// Position par d�faut
	if (bIncludeViewport)
	{
		params["viewLatitude"] = parseFloat(jQuery("#map_center_latitude").val());
		params["viewLongitude"] = parseFloat(jQuery("#map_center_longitude").val());
		params["viewZoom"] = parseFloat(jQuery("#map_zoom").val());
	}
	
	return params;
}
';

	return $getParams;
}

// Enregistrement des param�tres pass�s dans la requ�te
function mapimpl_mxn_prive_show_map_defaults_dist(&$uiElements, &$getParams, $profile = 'interface')
{
	$uiElements = _get_mxn_ui_elements($profile);
	$getParams = _get_mxn_get_params();
	return true;
}

?>
