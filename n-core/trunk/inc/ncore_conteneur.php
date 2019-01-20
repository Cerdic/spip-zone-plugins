<?php
/**
 * Ce fichier contient l'API N-Core de gestion des conteneurs.
 * Toutes ces fonctions sont aussi exposées en tant que filtres.
 *
 * @package SPIP\NCORE\CONTENEUR\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Calcule l'identifiant unique pour le conteneur sous forme de chaine.
 * Cette fonction est juste un wrapper pour le service `ncore_conteneur_identifier()`.
 * Elle est utilisée par les balises #NOISETTE_COMPILER et #CONTENEUR_IDENTIFIER.
 *
 * @api
 * @filtre
 *
 * @uses ncore_conteneur_verifier()
 * @uses ncore_conteneur_identifier()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Tableau associatif descriptif du conteneur.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return string
 *        Identifiant du conteneur ou chaine vide en cas d'erreur.
 */
function conteneur_identifier($plugin, $conteneur, $stockage = '') {

	// Wrapper sur la fonction de service homonyme avec une vérification préalable
	// pour éviter de le faire danschaque plugin utilisateur.
	include_spip('ncore/ncore');
	$id_conteneur =  ncore_conteneur_verifier($plugin, $conteneur, $stockage)
		? ncore_conteneur_identifier($plugin, $conteneur, $stockage)
		: '';

	return $id_conteneur;
}

/**
 * Reconstruit le conteneur sous forme de tableau à partir de son identifiant unique (fonction inverse
 * de `conteneur_identifier`).
 * Cette fonction est juste un wrapper pour le service `ncore_conteneur_construire()` mais est très utilisée
 * par les plugins utilisateurs.
 *
 * @api
 * @filtre
 *
 * @uses ncore_conteneur_construire()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $id_conteneur
 *        Identifiant unique du conteneur.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Tableau représentatif du conteneur ou tableau vide en cas d'erreur.
 */
function conteneur_construire($plugin, $id_conteneur, $stockage = '') {

	// Wrapper sur la fonction de service homonyme.
	include_spip('ncore/ncore');
	$conteneur = ncore_conteneur_construire($plugin, $id_conteneur, $stockage);

	return $conteneur;
}

/**
 * Détermine si un conteneur est une noisette ou pas.
 *
 * @api
 * @filtre
 *
 * @uses ncore_conteneur_construire()
 * @uses ncore_conteneur_verifier()
 * @uses ncore_conteneur_est_noisette()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string|array $conteneur
 *        Identifiant unique du conteneur ou tableau du conteneur.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return bool
 *        `true` si le conteneur est une noisette `false` sinon.
 */
function conteneur_est_noisette($plugin, $conteneur, $stockage = '') {

	// Suivant le format du conteneur on calcule le tableau ou on le vérifie.
	include_spip('ncore/ncore');
	if (is_string($conteneur)) {
		$conteneur = ncore_conteneur_construire($plugin, $conteneur, $stockage);
	} else {
		$conteneur = ncore_conteneur_verifier($plugin, $conteneur, $stockage);
	}

	// On appelle le service de N-Core qui est le seul service a ne pas être surchargeable par un plugin utilisateur.
	$est_noisette = ncore_conteneur_est_noisette($plugin, $conteneur, $stockage);

	return $est_noisette;
}

/**
 * Supprime toutes les noisettes d’un conteneur.
 * L'éventuelle imbrication de conteneurs est gérée dans la fonction de service ncore_conteneur_destocker().
 *
 * @api
 * @filtre
 *
 * @uses ncore_conteneur_construire()
 * @uses ncore_conteneur_verifier()
 * @uses ncore_conteneur_destocker()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $conteneur
 *        Tableau descriptif du conteneur ou identifiant du conteneur.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return bool
 */
function conteneur_vider($plugin, $conteneur, $stockage = '') {

	// Initialisation du retour
	$retour = false;

	// Suivant le format du conteneur on calcule le tableau ou on le vérifie.
	include_spip('ncore/ncore');
	if (is_string($conteneur)) {
		$conteneur = ncore_conteneur_construire($plugin, $conteneur, $stockage);
	} else {
		$conteneur = ncore_conteneur_verifier($plugin, $conteneur, $stockage);
	}

	if ($conteneur) {
		// On charge l'API de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		$retour = ncore_conteneur_destocker($plugin, $conteneur, $stockage);
	}

	return $retour;
}
