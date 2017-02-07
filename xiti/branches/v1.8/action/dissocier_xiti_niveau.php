<?php
/**
 * Gestion de l'action dissocier_xiti_niveau
 *
 * @package SPIP\Xiti\Action
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Dissocier un niveau 2
 *
 * @param string $arg
 *     fournit les arguments de la fonction supprimer_lien_xiti_niveau
 *     sous la forme `$id_objet-$objet-$xiti_niveau
 *
 * @return void
 */
function action_dissocier_xiti_niveau_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$arg = explode('-', $arg);
	list($id_objet, $objet, $xiti_niveau) = $arg;

	if ($id_objet = intval($id_objet)
		and autoriser('dissocierxitiniveau', $objet, $id_objet)
	) {
		supprimer_lien_xiti_niveau($xiti_niveau, $objet, $id_objet);
	} else {
		spip_log("Interdit de modifier $objet $id_objet", 'spip');
	}
}

/**
 * Supprimer un lien entre un niveau de xiti et un objet
 *
 * @param int $id_xiti_niveau
 * @param string $objet
 * @param int $id_objet
 * @return bool
 */
function supprimer_lien_xiti_niveau($id_xiti_niveau, $objet, $id_objet, $supprime = false, $check = false) {
	if (!$id_xiti_niveau = intval($id_xiti_niveau)) {
		return false;
	}

	// D'abord on ne supprime pas, on dissocie
	include_spip('action/editer_liens');
	objet_dissocier(array('xiti_niveau' => $id_xiti_niveau), array($objet => $id_objet), array('role' => '*'));

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_xiti_niveau/$id_xiti_niveau'");

	pipeline(
		'post_edition',
		array(
			'args' => array(
				'operation' => 'delier_xiti_niveau', // compat v<=2
				'action' => 'delier_xiti_niveau',
				'table' => 'spip_xiti_niveaux',
				'id_objet' => $id_xiti_niveau,
				'objet' => $objet,
				'id' => $id_objet
			),
			'data' => null
		)
	);
}
