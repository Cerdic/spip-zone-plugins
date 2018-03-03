<?php
/**
 * Pipelines de plugin Logos Rôles
 *
 * @plugin     logos_roles
 * @copyright  2016
 * @author     bystrano
 * @licence    GNU/GPL
 */

/**
 * Insérer du js dans le head de l'espace privé
 *
 * @pipeline jquery_plugins
 * @param  array $scripts Données du pipeline
 * @return array       Données du pipeline
 */
function logos_roles_jquery_plugins($scripts) {

	$scripts[] = 'javascript/logos_roles.js';

	return $scripts;
}

/**
 * Insérer du css dans le head de l'espace privé
 *
 * @pipeline header_prive
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function logos_roles_header_prive($flux) {

	$flux .= '<link rel="stylesheet" href="'
		. find_in_path('css/logos_roles.css')
		. '" type="text/css" media="all" />';

	return $flux;
}

/**
 * Préconfigurer le formulaire de massicotage pour utiliser le bon format
 *
 * @pipeline formulaire_charger
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function logos_roles_formulaire_charger($flux) {

	$form = $flux['args']['form'];
	$data = $flux['data'];

	if (($form === 'massicoter_image')
			and ($dimensions = get_dimensions_role($data['role']))) {
		$flux['data']['format'] = $dimensions['largeur'] . ':' . $dimensions['hauteur'];
		$flux['data']['forcer_dimensions'] = $dimensions;
	}

	return $flux;
}

/**
 * Rétablir les logos du site, qui sont systématiquement effacés dans le CRON
 * d'optimisation.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function logos_roles_optimiser_base_disparus($flux) {

	include_spip('inc/config');

	foreach (lire_config('logos_site') as $role => $id_document) {
		sql_insertq(
			'spip_documents_liens',
			array(
				'id_document' => intval($id_document),
				'objet' => 'site',
				'id_objet' => 0,
				'role' => $role,
			)
		);
	}

	return $flux;
}
