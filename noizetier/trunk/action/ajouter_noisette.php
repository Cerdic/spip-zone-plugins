<?php
/**
 * Action ajouter une noisette en ajax.
 *
 * Crée la noisette dans un conteneur donné à un rang donné.
 * Retourne du JSON.
 *
 * @plugin     Noizetier
 * @copyright  2018
 * @licence    GNU/GPL
 * @package    SPIP\Noizetier\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 *
 * @return void
  */
function action_ajouter_noisette_dist() {

	// Initialisation de la variable d'état de la fonction
	$retour = array(
		'done'    => 'false',
		'success' => array(),
		'errors'  => array(),
	);

	// Récupération des inputs du formulaire d'ajout
	$type_noisette = _request('_type_noisette');
	$id_conteneur = _request('_id_conteneur');
	$rang = intval(_request('rang'));

	// Décomposition de l'id du conteneur en éléments du noiZetier
	include_spip('inc/noizetier_conteneur');
	$conteneur = noizetier_conteneur_decomposer($id_conteneur);

	// Test de l'autorisation
	if (!autoriser('configurerpage', 'noizetier', '', 0, $conteneur)) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Ajout de la noisette au conteneur choisi.
	include_spip('inc/ncore_noisette');
	$id_noisette = noisette_ajouter('noizetier', $type_noisette, $conteneur, $rang);

	// Préparation du tableau de retour pour l'envoi en JSON.
	if (intval($id_noisette)) {
		$retour['done'] = true;
		$retour['success'] = array($id_noisette);
	} else {
		$retour['errors'] = array(_T('noizetier:erreur_ajout_noisette', array('noisettes' => $type_noisette)));
	}

	header('Content-Type: application/json; charset=' . $GLOBALS['meta']['charset']);
	echo json_encode($retour);
}
