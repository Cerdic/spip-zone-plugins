<?php
/**
 * Ce fichier contient l'action `deplacer_noisette` lancée par un utilisateur pour
 * déplacer une noisette d'un rang donné dans un conteneur à un autre rang dans le même
 * conteneur ou dans un conteneur différent (drag'n drop).
 *
 * @package SPIP\NOIZETIER\NOISETTE\ACTION
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Action déplacer une noisette en ajax
 *
 * Permet de déplacer une noisette avec n'importe quel rang, dans n'importe quel conteneur
 * Met à jour les rangs des autres noisettes si nécessaire.
 * Retourne du JSON.
 *
 * @Note : cette action diffère de decaler_noisette.php qui permet de déplacer d'un unique rang, au sein du même conteneur
 *
 * @return
 */
function action_deplacer_noisette_dist() {

	// Initialisation des variables d'état de la fonction
	$done = false;
	$success = $errors = array();

	// Récupération des inputs
	$id_noisette = _request('_id_noisette');
	$rang = intval(_request('rang'));
	$id_conteneur_destination = _request('_id_conteneur_destination');
	$id_conteneur_origine     = _request('_id_conteneur_origine');

	// Test de l'autorisation
	include_spip('inc/noizetier_conteneur');
	if (autoriser('configurerpage', 'noizetier', '', 0, noizetier_conteneur_decomposer($id_conteneur_origine))
	or autoriser('configurerpage', 'noizetier', '', 0, noizetier_conteneur_decomposer($id_conteneur_destination))) {
		// Déplacement de la noisette dans le conteneur destination au rang choisi.
		include_spip('inc/ncore_noisette');
		$deplacer = noisette_deplacer('noizetier', $id_noisette, $id_conteneur_destination, $rang);

		if ($deplacer) {
			$done = true;
			$success = array($id_noisette);
		} else {
			// TODO : remettre le rang d'origine
			$errors = array(_T('erreur'));
		}
	} else {
		$errors = array(_T('erreur'));
	}

	return envoyer_json_envoi(array(
		'done'    => $done,
		'success' => $success,
		'errors'  => $errors,
	));
}

/**
 * @param $data
 *
 * @return void
 */
function envoyer_json_envoi($data) {
	header('Content-Type: application/json; charset=' . $GLOBALS['meta']['charset']);
	echo json_encode($data);
}