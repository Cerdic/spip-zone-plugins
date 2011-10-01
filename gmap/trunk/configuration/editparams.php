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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');

//
// Options des rubriques
//

function configuration_editparams_dist()
{
	$corps = "";
	
	// Si on a le résultat d'un traitement, l'afficher ici
	$corps .= gmap_decode_result("msg_result");

	// Récupérer les paramètres
	$hack_modalbox = gmap_lire_config('gmap_edit_params', 'hack_modalbox', "oui");
	$sibling_same_parent = gmap_lire_config('gmap_edit_params', 'sibling_same_parent', "oui");
	$siblings_limit = gmap_lire_config('gmap_edit_params', 'siblings_limit', "5");
		
	// Paramétrage de l'accès
	$corps .= '
<fieldset class="config_group">
	<legend>'._T('gmap:configuration_edit_params_access').'</legend>
	<div class="padding"><div class="interior">
		<input type="checkbox" name="hack_modalbox" id="hack_modalbox" value="oui"'.(($hack_modalbox==="oui")?'checked="checked"':'').' />&nbsp;<label for="hack_modalbox">'._T('gmap:choix_hack_modalbox').'</label>
		<p class="explications">'._T('gmap:explication_hack_modalbox').'</p>
	</div></div>
</fieldset>';

	// Voisins
	$corps .= '
<fieldset class="config_group">
	<legend>'._T('gmap:configuration_edit_params_siblings').'</legend>
	<div class="padding"><div class="interior">
		<p><input type="checkbox" name="sibling_same_parent" id="sibling_same_parent" value="oui"'.(($sibling_same_parent==="oui")?'checked="checked"':'').' />&nbsp;<label for="sibling_same_parent">'._T('gmap:choix_sibling_same_parent').'</label></p>
		<p><label for="siblings_limit">'._T('gmap:choix_siblings_limit').'</label>&nbsp;<input type="text" name="siblings_limit" id="siblings_limit" value="'.$siblings_limit.'" /></p>
	</div></div>
</fieldset>';
	
	return gmap_formulaire_ajax('config_bloc_gmap', 'editparams', 'configurer_gmap', $corps,
		find_in_path('images/logo-config-editparams.png'),
		_T('gmap:configuration_edit_params'));
}
?>
