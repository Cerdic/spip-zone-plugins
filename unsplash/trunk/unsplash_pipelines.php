<?php

/**
 * Utilisations de pipelines par Unsplash.
 *
 * @plugin     Unsplash
 *
 * @copyright  2015-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajout de liste sur la vue d'un auteur.
 *
 * @pipeline affiche_auteurs_interventions
 *
 * @param array $flux Données du pipeline
 *
 * @return array Données du pipeline
 */
function unsplash_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/unsplash', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('unsplash:info_unsplash_auteur'),
		), array('ajax' => true));
	}

	return $flux;
}

/**
 * Insérer font-awesome dans le header privé
 *
 * @param $flux
 *
 * @return string
 */
function unsplash_header_prive($flux) {
	$flux .= "<link rel='stylesheet' id='font-awesome-css'  href='" . find_in_path('lib/font-awesome/css/font-awesome.min.css') . "' type='text/css' media='all' />";

	return $flux;
}

/**
 * Insérer les boutons d'ajout de logo pour l'objet
 *
 * @param $flux
 *
 * @return string
 */
function unsplash_boite_infos($flux) {

	$config = lire_config('unsplash/objets');
	$activer_logos = lire_config('activer_logos');
	$activer_logos_survol = lire_config('activer_logos_survol');
	$type = trouver_objet_exec($flux['args']['type']);
	$_id_objet = $flux['args']['id'];

	if (isset($config) and count($config) > 0 and $activer_logos == 'oui' and $type !== false) {
		include_spip('base/objets');
		if (in_array(table_objet_sql($type['table_objet_sql']), $config)) {
			/*
			 * On vérifie que l'objet a un logo normal et un logo de survol
			 * Cela conditionnera l'affichage de l'insertion de unsplash dans la boite d'infos de l'objet
			 */
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
			$contexte['logo_normal'] = false;
			$contexte['logo_survol'] = false;
			$_logo_normal = $chercher_logo($_id_objet, $type['id_table_objet'], 'on');
			$_logo_survol = $chercher_logo($_id_objet, $type['id_table_objet'], 'off');
			if (count($_logo_normal) > 0) {
				$contexte['logo_normal'] = true;
			}
			// Il faut que les logos de survol aient été activé dans la configuration du site
			if (count($_logo_survol) > 0 and $activer_logos_survol == 'oui') {
				$contexte['logo_survol'] = true;
			}
			$contexte['associer_objet'] = objet_type($type['type']) . '|' . $_id_objet;
			$contexte['objet'] = objet_type($type['type']);
			$contexte['id_objet'] = $_id_objet;
			$fond = recuperer_fond('prive/squelettes/inclure/unsplash_objet_logo', $contexte);
			$flux['data'] .= $fond;
		}
	}

	return $flux;
}

