<?php
/**
 * Utilisations de pipelines par Secteur par langue
 *
 * @plugin     Secteur par langue
 * @copyright  2019 - 2020
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Secteur_langue\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajouter des contenus dans la partie <head> des pages de l’espace privé.
 *
 * @param array $flux
 *   Données du pipeline.
 *
 * @return array
 *   Données du pipelin.
 */
function secteur_langue_header_prive($flux) {
	$flux .='
	<script type="text/javascript">
		$(document).ready(function() {
			$(".avis_source").click( function() {
				javascript:alert("'._T('secteur_langue:avis_rubrique_source').'");
			});
		});
	</script>
	';
	return $flux;
}

/**
 * Agit lors de l’édition d’un élément éditorial, lorsque l’utilisateur édite les champs ou change le statut de l’objet.
 * Il est appelé juste avant l’enregistrement des données.
 * On peut s’en servir pour contrôler ou modif
 *
 * @pipeline pre_edition
 *
 * @param array $flux
 *   Les données du pipeline
 *
 * @return array
 *   Les données du pipeleine.
 */
function secteur_langue_pre_edition($flux) {
	$table = $flux['args']['table'];
	// Tout objet dépendant d'une rubrique hérite automatiquement sa langue.
	if ($trouver_table = charger_fonction('trouver_table', 'base') AND
			$desc = $trouver_table($table) AND
			isset($desc['field']['id_rubrique'])) {
		$identifiant =  id_table_objet($table);
		$rubrique_parente = sql_fetsel('id_rubrique,lang,' . $identifiant, $table, $identifiant . '=' . $flux['args']['id_objet']);
		$id_rubrique = _request('id_parent') ? _request('id_parent')  : $rubrique_parente['id_rubrique'];
		$lang = sql_getfetsel(
				'lang',
				'spip_rubriques',
				'id_rubrique=' . $id_rubrique);
		if ($lang != $rubrique_parente['lang']) {
			$flux['data']['lang'] = $lang;
		}
	}

	return $flux;
}

/**
 * Modifier le tableau retourné par la fonction traiter d’un formulaire CVT ou effectuer des traitements supplémentaires.
 *
 * @param array $flux
 *   Les données du pipeline
 *
 * @return array
 *   Les données du pipeleine.
 */
function secteur_langue_formulaire_traiter($flux) {
	$form= $flux['args']['form'];
	// Assurer que la langue enregistré soit celle de la lang_dest, ne fonctionnait plus automatiquement sous spip 3.3.
	if ($form == 'editer_rubrique' AND $lang = _request('lang_dest')) {
		sql_updateq('spip_rubriques', ['lang' => $lang, 'langue_choisie' => 'oui'], 'id_rubrique=' . $flux['data']['id_rubrique']);
	}

	return $flux;
}

