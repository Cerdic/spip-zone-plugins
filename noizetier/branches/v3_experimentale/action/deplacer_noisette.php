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
 * @return void
 */
function action_deplacer_noisette_dist() {

	// Initialisation des variables d'état de la fonction
	$retour = array(
		'done'    => 'false',
		'success' => array(),
		'errors'  => array(),
	);

	// Récupération des inputs
	$id_noisette = _request('_id_noisette');
	$rang = intval(_request('rang'));
	$id_conteneur_destination = _request('_id_conteneur_destination');
	$id_conteneur_origine     = _request('_id_conteneur_origine');

	// Test de l'autorisation
	include_spip('inc/noizetier_conteneur');
	if (!autoriser('configurerpage', 'noizetier', '', 0, conteneur_noizetier_decomposer($id_conteneur_origine))
	or !autoriser('configurerpage', 'noizetier', '', 0, conteneur_noizetier_decomposer($id_conteneur_destination))) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Déplacement de la noisette dans le conteneur destination au rang choisi.
	include_spip('inc/ncore_noisette');
	$deplacer = noisette_deplacer('noizetier', $id_noisette, $id_conteneur_destination, $rang);

	if ($deplacer) {
		$retour['done'] = true;
		$retour['success'] = array($id_noisette);
	} else {
		$retour['errors'] = array(_T('noizetier:erreur_deplacement_noisette', array('noisette' => $id_noisette)));
	}

	header('Content-Type: application/json; charset=' . $GLOBALS['meta']['charset']);
	echo json_encode($retour);
}
