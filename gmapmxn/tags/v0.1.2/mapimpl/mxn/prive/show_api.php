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

// Enregistrement des paramètres passés dans la requête
function mapimpl_mxn_prive_show_api_dist()
{
	$corps = "";
	
	// Lire la configuration
	$provider = gmap_lire_config('gmap_api_mxn', 'provider', "openlayers");

	// Paramétrage du provider
	$jsOptions = '
var providers = Array();';
	$corps .= '
		<div class="config_group">
			<label for="provider">'._T('gmapmxn:api_provider').'</label>
			<select name="provider" id="provider" size="1">';
	$hiddenKeys = '';
	$warnings = '';
	foreach ($GLOBALS['allowed_providers'] as $name => $infos)
	{
		$corps .= '
				<option value="'.$name.'"'.(!strcmp($provider, $name) ? ' selected="selected"' : '').'>'.$infos['desc'].'</option>';
		$jsOptions .= '
providers["'.$name.'"] = new Array();
providers["'.$name.'"]["key"] = "'.$infos['key'].'";
providers["'.$name.'"]["geocoder"] = "'.$infos['geocoder'].'";
providers["'.$name.'"]["kml"] = "'.$infos['kml'].'";';
		if ($infos['key'] === 'oui')
			$hiddenKeys .= '
		<input type="hidden" name="provider_key_'.$name.'" id="provider_key_'.$name.'" value="'.gmap_lire_config('gmap_api_mxn', 'provider_key_'.$name, "").'" /></p>';
		$warning = _T('gmapmxn:warning_'.$name);
		if ($warning !== 'nop')
			$warnings .= '
		<p id="warning-'.$name.'" class="provider-warning warning">'.$warning.'</p>';
	}
	$corps .= '
			</select>
		</div>';

	// Information sur le provider choisi
	$corps .= '
		<div class="config_group">
			<p>'._T('gmapmxn:provider_caps').'</p>
			<div class="liste_choix">
				<input type="checkbox" name="provider_cap_markers" id="provider_cap_markers" value="oui" checked="checked" disabled="disabled" />&nbsp;<label for="provider_cap_markers">'._T('gmapmxn:provider_cap_markers').'</label><br/>
				<input type="checkbox" name="provider_cap_bubbles" id="provider_cap_bubbles" value="oui" checked="checked" disabled="disabled" />&nbsp;<label for="provider_cap_bubbles">'._T('gmapmxn:provider_cap_bubbles').'</label><br/>
				<input type="checkbox" name="provider_cap_kml" id="provider_cap_kml" value="oui" checked="checked" disabled="disabled" />&nbsp;<label for="provider_cap_kml">'._T('gmapmxn:provider_cap_kml').'</label><br/>
				<input type="checkbox" name="provider_cap_geocoder" id="provider_cap_geocoder" value="oui" checked="checked" disabled="disabled" />&nbsp;<label for="provider_cap_geocoder">'._T('gmapmxn:provider_cap_geocoder').'</label><br/>
			</div>';
	$corps .= $warnings;
	$corps .= '
		</div>';
		
	// Saisie d'une clef d'activation si nécessaire
	$corps .= $hiddenKeys;
	$corps .= '
		<div class="config_group" id="provider_key_panel">
			<p><label for="provider_key">'._T('gmapmxn:provider_key').'</label><br />
			<input type="text" name="provider_key" id="provider_key" value="" style="width:100%;" /></p>
		</div>';
	
	// Script qui cahe ou montre les parties en fonction du provider
	$corps .= '
<script type="text/javascript">
//<![CDATA[
'.$jsOptions.'
function updateProviderCaps()
{
	var provider = jQuery("#provider").val();
	if (providers[provider])
	{
		if (providers[provider]["kml"] === "oui")
			jQuery("#provider_cap_kml").attr("checked", "checked");
		else
			jQuery("#provider_cap_kml").removeAttr("checked");
		if (providers[provider]["geocoder"] === "oui")
			jQuery("#provider_cap_geocoder").attr("checked", "checked");
		else
			jQuery("#provider_cap_geocoder").removeAttr("checked");
		if (providers[provider]["key"] === "oui")
		{
			jQuery("#provider_key_panel").show();
			jQuery("#provider_key").val(jQuery("#provider_key_"+provider).val());
		}
		else
			jQuery("#provider_key_panel").hide();
		jQuery(".provider-warning").hide();
		jQuery("#warning-"+provider).show();
	}
}
jQuery(document).ready(function()
{
	updateProviderCaps();
	jQuery("#provider").change(function() {
		updateProviderCaps();
	});
	jQuery("#provider_key").focusout(function() {
		var provider = jQuery("#provider").val();
		jQuery("#provider_key_"+provider).val(jQuery("#provider_key").val());
	});
});
//]]>
</script>';
	
	return $corps; 
}

?>