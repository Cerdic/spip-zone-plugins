<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Interface de configuration de l'API pour Google Maps v3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Initialisation du paramétrage pour gma3
function mapimpl_gma3_prive_api_initialiser_dist()
{
	gmap_init_config('gmap_api_gma3', 'key', '');
	gmap_init_config('gmap_api_gma3', 'version', '3');
}

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma3_prive_api_recuperer_dist()
{
	$corps = "";
	
	// Lire la configuration
	$key = gmap_lire_config('gmap_api_gma3', 'key', "");
	$version = gmap_lire_config('gmap_api_gma3', 'version', "3");
	$subversion = "";
	if (preg_match('^3.\d+^', $version))
	{
		$parts = explode('.', $version);
		$subversion = $parts[1];
		$version = "3.";
	}
	
	$corps .= '
	<ul>';

	// Paramétrage de la clef
	$corps .= '
		<li class="cle_api pleine_largeur obligatoire">
			<label for="cle_api">'._T('gmap:clef_V3').'</label>
			<input type="text" name="cle_api" class="text" value="'.$key.'" id="cle_api" />
			<p class="explication">'._T('gmap:explication_clef_V3').'</p>
		</li>';
	
	// Paramétrage de la version
	$corps .= '
		<script type="text/javascript">
		//<![CDATA[
		// Evènements jQuery sur le chargement du document
		jQuery(document).ready(function()
		{
			jQuery(\'#api_version\').change(function()
			{
				if (jQuery(this).val() == "3.")
					jQuery(\'.num_other_version_container\').show();
				else
					jQuery(\'.num_other_version_container\').hide();
			});
		});
		//]]>
		</script>';
	$corps .= '
		<li class="api_version">
			<label for="api_version">'._T('gmap:api_version').'</label>
			<select name="api_version" id="api_version" size="1">
				<option value="3"'.(!strcmp($version, "3") ? ' selected="selected"' : '').'>'._T('gmap:current_version_gma3').'</option>
				<option value="3."'.(!strcmp($version, "3.") ? ' selected="selected"' : '').'>'._T('gmap:other_version_gma3').'</option>
			</select>';
	$extraBloc = "none";
	if ($subversion != "")
		$extraBloc = "block";
	$corps .= '
			<ul style="display:'.$extraBloc.'" class="sub-bloc num_other_version_container">
				<li class="num_other_version">
					<label for="num_other_version">'._T('gmap:api_sub_version_gma3').'</label>
					<input type="text" name="num_other_version" class="text" value="'.$subversion.'" id="num_other_version" />
				</li>
			</ul>';
	$corps .= '
			<p class="explication">'._T('gmap:explication_api_version_v3').'</p>
		</li>';

	$corps .= '
	</ul>';
		
	return $corps; 
}

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma3_prive_api_traiter_dist()
{
	gmap_ecrire_config('gmap_api_gma3', 'key', _request('cle_api'));
	$version = _request('api_version');
	if ($version == "3.")
		$version .= _request('num_other_version');
	gmap_ecrire_config('gmap_api_gma3', 'version', $version);
}

?>
