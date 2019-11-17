<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajoute le liens vers le tableau sur les pages des formulaires et des listes de réponses
 * @param array $flux
 * @return array flux_modifie
 **/
function formidable_ts_affiche_gauche($flux) {
	$args = $flux['args'];
	if (in_array($args['exec'], array( 'formulaire', 'formulaires_analyse', 'formulaires_reponses'))) {
		$boite_fermer = boite_fermer();
		$url = parametre_url(generer_url_ecrire('formidable_tablesorter'),'id_formulaire',$args['id_formulaire']);
		$lien = icone_horizontale(_T('formidable_ts:tableau_reponses'), $url, 'formulaire-reponses-24');
		$flux['data'] = str_replace($boite_fermer,"$lien\n\r$boite_fermer", $flux['data']);
	}
	return $flux;
}
