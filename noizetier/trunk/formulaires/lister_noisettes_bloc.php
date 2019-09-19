<?php
/**
 * Ce fichier contient le formulaire listant les noisettes incluses dans un conteneur de type (page, bloc)
 * ou (objet, bloc).
 * Ce formulaire autorise le déplacement de noisette ou l'ajout de noisette dans le conteneur par simple
 * glisser-déposer.
 *
 * @package SPIP\NOIZETIER\NOISETTE\FORMULAIRE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * La fonction charger déclare les champs postés et y intègre les valeurs par défaut.
 *
 * @param array|string $page_ou_objet
 * 		Page au sens SPIP ou objet spécifiquement identifié.
 *      - dans le cas d'une page SPIP comme sommaire, l'argument est une chaîne.
 * 		- dans le cas d'un objet SPIP comme un article d'id x, l'argument est un tableau associatif à deux index,
 *        `objet` et `id_objet`.
 * @param string       $bloc
 * 		Bloc de page au sens Z.
 *
 * @return array
 * 		Tableau des champs postés pour l'affichage du formulaire.
 */
function formulaires_lister_noisettes_bloc_charger_dist($page_ou_objet, $bloc) {

	// Si on est en présence d'une page au sens SPIP, il faut convertir l'identifiant en tableau.
	// Sinon, on est en présence d'un objet précis connu par son type et son id fourni dans un
	// tableau.
	$valeurs = is_array($page_ou_objet) ? $page_ou_objet : array('page' => $page_ou_objet);

	// Ajout du bloc recevant les noisettes
	$valeurs['bloc'] = $bloc;

	// Ajout de l'identifiant du conteneur qui servira à la boucle des noisettes
	include_spip('inc/noizetier_conteneur');
	$valeurs['id_conteneur'] = conteneur_noizetier_composer($page_ou_objet, $bloc);
	
	return $valeurs;
}


/**
 * @param array|string $page_ou_objet
 * 		Page au sens SPIP ou objet spécifiquement identifié.
 *      - dans le cas d'une page SPIP comme sommaire, l'argument est une chaîne.
 * 		- dans le cas d'un objet SPIP comme un article d'id x, l'argument est un tableau associatif à deux index,
 *        `objet` et `id_objet`.
 * @param string       $bloc
 * 		Bloc de page au sens Z.
 *
 * @return array
 * 		Tableau des champs postés pour l'affichage du formulaire.
 */
function formulaires_lister_noisettes_bloc_traiter_dist($page_ou_objet, $bloc) {

	$retour = array();

	// Récupération de l'identifiant du conteneur constitué par le bloc en cours de configuration
	// et conversion en tableau associatif.
	include_spip('inc/noizetier_conteneur');
	$id_conteneur = _request('conteneur_id');
	$conteneur = conteneur_noizetier_decomposer($id_conteneur);

	if (!autoriser('configurerpage', 'noizetier', 0, '', $conteneur)) {
		$retour['message_erreur'] = _T('noizetier:probleme_droits');
	}

	return $retour;
}
