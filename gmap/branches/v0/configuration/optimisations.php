<?php
/*
 * GMap plugin
 * Insertion de carte sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Page de paramétrage des optimisations
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');

function configuration_optimisations_dist()
{
	$corps = "";
	
	// Si on a le résultat d'un traitement, l'afficher ici
	$corps .= gmap_decode_result("msg_result");
	
	// Texte explicatif
	$corps .= '
	<div class="texte"><p>'._T("gmap:configuration_optimisations_explic").'</p></div>';

	// Gérer l'état sélectionné des marqueurs (donc envoi des icones en plus)
	$gerer_selection = gmap_lire_config('gmap_optimisations', 'gerer_selection', 'oui');
	$corps .= '
	<div class="config_group">
		<input type="checkbox" name="gerer_selection" id="gerer_selection" value="oui"'.(($gerer_selection === 'oui') ? ' checked="checked"' : '').' />&nbsp;<label for="gerer_selection">'._T('gmap:gerer_selection').'</label>
		<p class="explications droite">'._T('gmap:gerer_selection_explic').'</p>
	</div>';
	
	// Recherche selonn la branche
	$gerer_branches = gmap_lire_config('gmap_optimisations', 'gerer_branches', 'oui');
	$corps .= '
	<div class="config_group">
		<input type="checkbox" name="gerer_branches" id="gerer_branches" value="oui"'.(($gerer_branches === 'oui') ? ' checked="checked"' : '').' />&nbsp;<label for="gerer_branches">'._T('gmap:gerer_branches').'</label>
		<p class="explications droite">'._T('gmap:gerer_branches_explic').'</p>
	</div>';
	
	return gmap_formulaire_ajax('config_bloc_gmap', 'optimisations', 'configurer_gmap', $corps,
		find_in_path('images/logo-config-optimisations.png'),
		_T('gmap:configuration_optimisations'));
}

?>
