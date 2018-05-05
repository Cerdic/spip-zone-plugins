<?php
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// --------------------------------------------------------------------------------
// --------------------- API TYPES DE NOISETTE : COMPLEMENT -----------------------
// --------------------------------------------------------------------------------
include_spip('inc/noizetier_type_noisette');


// --------------------------------------------------------------------------------
// ------------------------- API NOISETTES : COMPLEMENT ---------------------------
// --------------------------------------------------------------------------------


// --------------------------------------------------------------------------------
// ------------------------- API CONTENEURS : COMPLEMENT --------------------------
// --------------------------------------------------------------------------------


// -------------------------------------------------------------------
// --------------------------- API ICONES ----------------------------
// -------------------------------------------------------------------

/**
 * Retourne le chemin complet d'une icone.
 * La fonction vérifie d'abord que l'icone est dans le thème du privé (chemin_image),
 * sinon cherche dans le path SPIP (find_in_path).
 *
 * @package SPIP\NOIZETIER\API\ICONE
 * @api
 * @filtre
 *
 * @param string $icone
 *
 * @return string
 */
 function noizetier_icone_chemin($icone){
	// TODO : faut-il garder cette fonction ou simplifier en utilisant uniquement chemin_image() ?
	if (!$chemin = chemin_image($icone)) {
		$chemin = find_in_path($icone);
	}

	return $chemin;
}

/**
 * Liste d'icones d'une taille donnée en pixels obtenues en fouillant dans les thème
 * spip du privé.
 *
 * @package SPIP\NOIZETIER\API\ICONE
 * @api
 * @filtre
 *
 * @param $taille	int
 * 		Taille en pixels des icones à répertorier.
 *
 * @return array
 * 		Tableau des chemins complets des icones trouvés dans le path SPIP.
 */
function noizetier_icone_repertorier($taille = 24) {
	static $icones = null;

	if (is_null($icones)) {
		$pattern = ".+-${taille}[.](jpg|jpeg|png|gif)$";
		$icones = find_all_in_path('prive/themes/spip/images/', $pattern);
	}

	return $icones;
}


// -------------------------------------------------------------------
// ---------------------------- API BLOCS ----------------------------
// -------------------------------------------------------------------
include_spip('inc/noizetier_bloc');

// -------------------------------------------------------------------
// ---------------------------- API PAGES ----------------------------
// -------------------------------------------------------------------
include_spip('inc/noizetier_page');


// --------------------------------------------------------------------
// ---------------------------- API OBJETS ----------------------------
// --------------------------------------------------------------------
include_spip('inc/noizetier_objet');


// --------------------------------------------------------------------
// ------------------------- API CONFIGURATION ------------------------
// --------------------------------------------------------------------

/**
 * Détermine si la configuration d'une page ou d'une noisette contenue dans son
 * fichier XML ou YAML a été modifié ou pas.
 *
 * @package SPIP\NOIZETIER\API\PAGE
 * @api
 * @filtre
 *
 * @param string $entite
 * 		`page` pour désigner une page ou `noisette` pour une noisette.
 * @param string $identifiant
 * 		Identifiant de la page ou de la noisette.
 *
 * @return boolean
 * 		`true` si la configuration a été modifiée, `false` sinon.
 */
 // TODO : a voir si cette fonction n'est pas utilisée pour les noisettes on la renommera en noizetier_page_modifiee()
function noizetier_configuration_est_modifiee($entite, $identifiant) {

	$est_modifiee = true;

	// Détermination du répertoire par défaut
	$repertoire = ($entite == 'page') ? noizetier_page_repertoire() : 'noisettes/';

	// Récupération du md5 enregistré en base de données
	$from = ($entite == 'page') ? 'spip_noizetier_pages' : 'spip_types_noisettes';
	$where = array($entite . '=' . sql_quote($identifiant));
	$md5_enregistre = sql_getfetsel('signature', $from, $where);

	if ($md5_enregistre) {
		// On recherche d'abord le fichier YAML qui est commun aux 2 entités et sinon le fichier
		// XML si c'est une page.
		if (($fichier = find_in_path("${repertoire}${identifiant}.yaml"))
		or (($entite == 'page') and ($fichier = find_in_path("${repertoire}${identifiant}.xml")))) {
			$md5 = md5_file($fichier);
			if ($md5 == $md5_enregistre) {
				$est_modifiee = false;
			}
		}
	}

	return $est_modifiee;
}


// --------------------------------------------------------------------
// ------------------------------ BALISES -----------------------------
// --------------------------------------------------------------------
include_spip('public/noizetier_balises');
