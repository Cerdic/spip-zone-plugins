<?php
/**
 * Ce fichier contient l'action `activer_composition` lancée par un webmestre pour
 * activer la création de composition sur un type d'objet donné.
 *
 * @package SPIP\NOIZETIER\COMPOSITION
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet d'activer de façon sécurisée l'utilisation de composition
 * sur un type d'objet.
 *
 * Cette action est réservée aux utilisateurs autorisés à configurer le plugin Compositions.
 * Elle nécessite de passer le type d'objet concerné.
 *
 * @uses compositions_objets_actives()
 *
 * @return void
 */
function action_activer_composition_dist() {

	// Securisation et autorisation.
	// L'argument attendu est le type d'objet à activer
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$type_objet = $securiser_action();

	// Verification des autorisations
	if (!autoriser('activercomposition', 'noizetier', 0, '', array('page' => $type_objet))) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// On récupère la liste des types d'objet pour lesquels les compositions sont actives.
	include_spip('compositions_fonctions');
	$types_objet_actives = compositions_objets_actives();

	// Si le type d'objet n'est pas encore actif, alors on le rajoute à la liste incluse
	// dans la meta 'compositions'
	if (!in_array($type_objet, $types_objet_actives)) {
		include_spip('inc/config');
		include_spip('base/objets');
		ecrire_config(
			'compositions/objets',
			array_merge(array_map('table_objet_sql', $types_objet_actives), array(table_objet_sql($type_objet))));
	}
}
