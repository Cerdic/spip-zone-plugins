<?php

// inc/exau_pipelines.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/exau_api');

/**
 * Placer dans le head en prive' le css et jss necessaires
 * @return string
 * @param string $flux
 */
function exau_header_prive ($flux) {

	if(_request('exec') == 'auteurs') {
		
		$statut = _request('statut');
		
		if($statut = exau_statut_correct ($statut)) {
		
			$url = generer_action_auteur('exau_export_auteurs', $statut);
		
			$titre = exau_generer_nom_fichier ($statut);
	
			$flux .= "\n"
			. "<style type='text/css'>\n"
			. "<!--\n"
			. "#export_auteurs {display:block; height:24px; padding-left:28px;  background: url("._DIR_PLUGIN_EXAU . "images/export_auteurs-24.png) no-repeat 0 0}\n"
			. "-->\n"
			. "</style>\n"	
			. "<meta id='x-exau-url' name='x-exau-url' content='$url' />\n"
			. "<script type=\"text/javascript\" src=\"" . _DIR_PLUGIN_EXAU . "javascript/exau.js\"></script>\n"
			;

		}
	}
	return($flux);
}

/**
 * Affiche le lien d'export 
 * dans la colonne de droite en espace prive'
 * @return array $flux
 * @param array $flux
 */
function exau_affiche_droite ($flux) {

	$exec = _request('exec');
	$statut = _request('statut');

	if ($exec == 'auteurs') {
	
		if($statut = exau_statut_correct ($statut)) {
	
			$bg = find_in_path('images/searching.gif');

			$flux['data'] .= "\n"
				. "<br />\n"
				. debut_cadre_enfonce('', true)
				. "<div style='background url($bg) no-repeat top right !important'>\n"
				. "<img id='exau-ajax-loader' src='$bg' style='position:absolute;top:6px;right:0;display:none' width='13' height='13' />\n"
				. "<a id='export_auteurs' href='" . generer_url_ecrire("exau_export_auteurs", "statut=".$statut) . "' class='cellule-h'>"
				. _T('exau:' . (($statut == EXAU_STATUTS_INVITES) ? "exporter_visiteurs" : "exporter_auteurs"))
				. "</a>\n"
				. "</div>\n"
				. fin_cadre_enfonce(true)
				;

		} // else spip_log("statut KO");
	}
	
	return($flux);
}


