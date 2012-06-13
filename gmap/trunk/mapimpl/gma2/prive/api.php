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
function mapimpl_gma2_prive_api_initialiser_dist()
{
	gmap_init_config('gmap_api_gma2', 'key', '');
	gmap_init_config('gmap_api_gma2', 'version', '2.s');
}

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma2_prive_api_recuperer_dist()
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

	$corps .= '
	<ul>';

	// Paramétrage de la clef
	$corps .= '
		<li class="cle_api pleine_largeur obligatoire">
			<label for="cle_api">'._T('gmap:clef').'</label><br />
			<input type="text" name="cle_api" class="text" value="'.$key.'" id="cle_api" />
			<p class="explication">'._T('gmap:explication_clef').'</p>
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
				if (jQuery(this).val() == "2.")
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
				<option value="2"'.(!strcmp($version, "2") ? ' selected="selected"' : '').'>'._T('gmap:current_version_gma2').'</option>
				<option value="2.x"'.(!strcmp($version, "2.x") ? ' selected="selected"' : '').'>'._T('gmap:latest_version_gma2').'</option>
				<option value="2.s"'.(!strcmp($version, "2.s") ? ' selected="selected"' : '').'>'._T('gmap:stable_version_gma2').'</option>
				<option value="2."'.(!strcmp($version, "2.") ? ' selected="selected"' : '').'>'._T('gmap:other_version_gma2').'</option>
			</select>';
	$extraBloc = "none";
	if ($subversion != "")
		$extraBloc = "block";
	$corps .= '
			<ul style="display:'.$extraBloc.'" class="sub-bloc num_other_version_container">
				<li class="num_other_version">
					<label for="num_other_version">'._T('gmap:api_sub_version_gma2').'</label>
					<input type="text" name="num_other_version" class="text" value="'.$subversion.'" id="num_other_version" />
				</li>
			</ul>';
	$corps .= '
			<p class="explication">'._T('gmap:explication_api_version').'</p>
		</li>';

	$corps .= '
	</ul>';
		
	return $corps; 
}

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma2_prive_api_traiter_dist()
{
	gmap_ecrire_config('gmap_api_gma2', 'key', _request('cle_api'));
	$version = _request('api_version');
	if ($version == "2.")
		$version .= _request('num_other_version');
	gmap_ecrire_config('gmap_api_gma2', 'version', $version);
}

?>
