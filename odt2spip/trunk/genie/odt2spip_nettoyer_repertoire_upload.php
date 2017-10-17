<?php
/**
 * Tache de nettoyages de fichiers du plugin Odt2SPIP
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Enlève les fichiers du répertoire de travail de odt2spip qui sont trop vieux
 *
 * @param int $last
 * @return int
 **/
function genie_odt2spip_nettoyer_repertoire_upload_dist($last) {

	odt2spip_nettoyer_repertoire_recursif(_DIR_TMP . 'odt2spip', 3600);

	return 1;
}



/**
 * Nettoyer un répertoire suivant l'age et le nombre de ses fichiers
 *
 * Nettoie aussi les sous répertoires.
 * Supprime automatiquement les répertoires vides.
 *
 * @param string $repertoire
 *     Répertoire à nettoyer
 * @param int $age_max
 *     Age maxium des fichiers en seconde. Par défaut 24*3600
 * @param int $max_files
 *     Nombre maximum de fichiers dans le dossier
 * @return bool
 *     - false : erreur de lecture du répertoire.
 *     - true : action réalisée.
 **/
function odt2spip_nettoyer_repertoire_recursif($repertoire, $age_max = 86400) {

	$repertoire = rtrim($repertoire, '/');
	if (!is_dir($repertoire)) {
		return false;
	}

	$fichiers = scandir($repertoire);
	if ($fichiers === false) {
		return false;
	}

	$fichiers = odt2spip_filtrer_fichiers($fichiers);
	if (!$fichiers) {
		supprimer_repertoire($repertoire);
		return true;
	}

	foreach ($fichiers as $fichier) {
		$chemin = $repertoire . DIRECTORY_SEPARATOR . $fichier;
		if (is_dir($chemin)) {
			odt2spip_nettoyer_repertoire_recursif($chemin, $age_max);
		}
		elseif (is_file($chemin) and !jeune_fichier($chemin, $age_max)) {
			supprimer_fichier($chemin);
		}
	}

	// à partir d'ici, on a pu possiblement vider le répertoire…
	// on le supprime s'il est devenu vide.
	$fichiers = scandir($repertoire);
	if ($fichiers === false) {
		return false;
	}

	$fichiers = odt2spip_filtrer_fichiers($fichiers);
	if (!$fichiers) {
		supprimer_repertoire($repertoire);
	}

	return true;
}


/**
 * Enlever d'une liste des fichiers ce qui est inutile
 *
 * Enlève les fichiers .. et . ainsi que des fichiers à
 * ne pas considérer comme importants pour tester qu'un
 * répertoire a du contenu.
 *
 * @param array $fichiers
 * @return array
 */
function odt2spip_filtrer_fichiers($fichiers) {
	return array_diff($fichiers, array('..', '.', '.ok'));
}