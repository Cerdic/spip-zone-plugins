<?php
/**
 * Utilisations de pipelines par Profils
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Optimiser la base de données
 *
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function profils_optimiser_base_disparus($flux) {
	sql_delete('spip_profils', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}

/**
 * Liste les saisies à ajouter au formulaire d'inscription
 *
 * @pipeline formulaire_saisies
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function profils_formulaire_saisies($flux) {
	if ($flux['args']['form'] == 'inscription') {
		include_spip('inc/profils');
		
		if ($saisies = profils_chercher_saisies_profil('inscription', 'new')) {
			$flux['data'] = $saisies;
		}
	}

	return $flux;
}

/**
 * Ajoute les champs au formulaire d'inscription
 *
 * @pipeline formulaire_fond
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function profils_formulaire_fond($flux) {
	if (
		$flux['args']['form'] == 'inscription'
		and $saisies = $flux['args']['contexte']['_saisies']
	) {
		// On génère le HTML des champs
		$contexte = $flux['args']['contexte'];
		$contexte['saisies'] = $contexte['_saisies'];
		unset($contexte['_saisies']);
		$champs = recuperer_fond('inclure/generer_saisies', $contexte);
		
		// On insère
		$flux['data'] = preg_replace(
			"|</fieldset>|Uims",
			"\\0" . $champs,
			$flux['data'],
			1
		);
	}

	return $flux;
}
