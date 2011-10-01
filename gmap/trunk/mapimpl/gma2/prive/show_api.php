<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Interface de configuration de l'interface pour Google Maps v3
 *
 * Usage :
 * $show_api = charger_fonction("show_api", "mapimpl/$api/prive");
 * echo $show_api();
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma2_prive_show_api_dist()
{
	$corps = "";
	
	// Lire la configuration
	$key = gmap_lire_config('gmap_api_gma2', 'key', "");
	$version = gmap_lire_config('gmap_api_gma2', 'version', "2");
	$subversion = "";
	if (preg_match('^2.\d+^', $version))
	{
		$parts = explode('.', $version);
		$subversion = $parts[1];
		$version = "2.";
	}

	// Paramétrage de la clef
	$corps .= '
		<div class="config_group">
			<label for="cle_api">'._T('gmap:clef').'</label><br />
			<input type="text" name="cle_api" class="text" value="'.$key.'" id="cle_api" width="60" />
			<p class="explications droite">'._T('gmap:explication_clef').'</p>
		</div>';
	
	// Paramétrage de la version
	$corps .= '
		<script type="text/javascript">
		//<![CDATA[
		// Evènements jQuery sur le chargement du document
		jQuery(document).ready(function()
		{
			jQuery(\'#api_version\').change(function()
			{
				if (jQuery(this).val() == "2.")
					jQuery(\'.num_other_version_container\').show();
				else
					jQuery(\'.num_other_version_container\').hide();
			});
		});
		//]]>
		</script>';
	$corps .= '
		<div class="config_group">
			<label for="api_version">'._T('gmap:api_version').'</label>
			<select name="api_version" id="api_version" size="1">
				<option value="2"'.(!strcmp($version, "2") ? ' selected="selected"' : '').'>'._T('gmap:current_version_gma2').'</option>
				<option value="2.x"'.(!strcmp($version, "2.x") ? ' selected="selected"' : '').'>'._T('gmap:latest_version_gma2').'</option>
				<option value="2.s"'.(!strcmp($version, "2.s") ? ' selected="selected"' : '').'>'._T('gmap:stable_version_gma2').'</option>
				<option value="2."'.(!strcmp($version, "2.") ? ' selected="selected"' : '').'>'._T('gmap:other_version_gma2').'</option>
			</select>';
	$extraBloc = "none";
	if ($subversion != "")
		$extraBloc = "block";
	$corps .= '
		<div style="display:'.$extraBloc.'" class="num_other_version_container">
			<label for="num_other_version">'._T('gmap:api_sub_version_gma2').'</label>
			<input type="text" name="num_other_version" class="text" value="'.$subversion.'" id="num_other_version" />
		</div>';
	$corps .= '
			<p class="explications droite">'._T('gmap:explication_api_version').'</p>
		</div>';

	return $corps; 
}

?>