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

function balise_NOIZETIER_NOISETTE_PREVIEW_dist($p) {
	$id_noisette = champ_sql('id_noisette', $p);
	$type_noisette = champ_sql('type_noisette', $p);
	$parametres = champ_sql('parametres', $p);

	$inclusion = "recuperer_fond(
		'noisette_preview',
		array_merge(unserialize($parametres), array('type_noisette' => $type_noisette))
	)";

	$p->code = "$inclusion";
	$p->interdire_scripts = false;

	return $p;
}


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

/**
 * Renvoie le nombre de noisettes de chaque bloc configurables d'une page, d'une composition
 * ou d'un objet.
 *
 * @api
 * @filtre
 *
 * @param string $page_ou_objet
 * 		L'identifiant de la page, de la composition ou de l'objet au format:
 * 		- pour une page : type
 * 		- pour une composition : type-composition
 * 		- pour un objet : type_objet-id_objet
 *
 * @return array
 */
function noizetier_bloc_compter_noisettes($page_ou_objet) {

	static $blocs_compteur = array();

	if (!isset($blocs_compteur[$page_ou_objet])) {
		// Initialisation des compteurs par bloc
		$nb_noisettes = array();

		// Le nombre de noisettes par bloc doit être calculé par une lecture de la table spip_noisettes.
		$from = array('spip_noisettes');
		$select = array('bloc', "count(type_noisette) as 'noisettes'");
		// -- Construction du where identifiant précisément la page ou l'objet concerné
		$identifiants = explode('-', $page_ou_objet);
		if (isset($identifiants[1]) and ($id = intval($identifiants[1]))) {
			// L'identifiant est celui d'un objet
			$where = array('objet=' . sql_quote($identifiants[0]), 'id_objet=' . $id);
		} else {
			if (!isset($identifiants[1])) {
				// L'identifiant est celui d'une page
				$identifiants[1] = '';
			}
			$where = array('type=' . sql_quote($identifiants[0]), 'composition=' . sql_quote($identifiants[1]));
		}
		$group = array('bloc');
		$compteurs = sql_allfetsel($select, $from, $where, $group);
		if ($compteurs) {
			// On formate le tableau [bloc] = nb noisettes
			foreach ($compteurs as $_compteur) {
				$nb_noisettes[$_compteur['bloc']] = $_compteur['noisettes'];
			}
		}
		$blocs_compteur[$page_ou_objet] = $nb_noisettes;
	}

	return (isset($blocs_compteur[$page_ou_objet]) ? $blocs_compteur[$page_ou_objet] : array());
}

// Cette balise renvoie la description complète ou l'info donnée d'un bloc
function balise_NOIZETIER_BLOC_INFOS_dist($p) {
	$bloc = interprete_argument_balise(1, $p);
	$bloc = str_replace('\'', '"', $bloc);
	$information = interprete_argument_balise(2, $p);
	$information = isset($information) ? str_replace('\'', '"', $information) : '""';
	$p->code = "calculer_infos_bloc($bloc, $information)";

	return $p;
}

function calculer_infos_bloc($bloc = '', $information = '') {

	include_spip('inc/noizetier_bloc');
	return noizetier_bloc_lire($bloc, $information);
}


// -------------------------------------------------------------------
// ---------------------------- API PAGES ----------------------------
// -------------------------------------------------------------------

// Cette balise renvoie le tableau de la liste des objets possédant des noisettes configurées
function balise_NOIZETIER_PAGE_INFOS_dist($p) {

	// Récupération des arguments de la balise.
	// -- seul l'argument information est optionnel.
	$page = interprete_argument_balise(1, $p);
	$page = str_replace('\'', '"', $page);
	$information = interprete_argument_balise(3, $p);
	$information = isset($information) ? str_replace('\'', '"', $information) : '""';

	// Calcul de la balise
	$p->code = "calculer_infos_page($page, $information)";

	return $p;
}

function calculer_infos_page($page, $information = '') {

	include_spip('inc/noizetier_page');
	return noizetier_page_lire($page, $information);
}


// --------------------------------------------------------------------
// ---------------------------- API OBJETS ----------------------------
// --------------------------------------------------------------------

// Cette balise renvoie le tableau de la liste des objets possédant des noisettes configurées
function balise_NOIZETIER_OBJET_INFOS_dist($p) {

	// Récupération des arguments de la balise.
	// -- seul l'argument information est optionnel.
	$objet = interprete_argument_balise(1, $p);
	$objet = str_replace('\'', '"', $objet);
	$id_objet = interprete_argument_balise(2, $p);
	$id_objet = isset($id_objet) ? $id_objet : '0';
	$information = interprete_argument_balise(3, $p);
	$information = isset($information) ? str_replace('\'', '"', $information) : '""';

	// Calcul de la balise
	$p->code = "calculer_infos_objet($objet, $id_objet, $information)";

	return $p;
}

function calculer_infos_objet($objet, $id_objet, $information = '') {

	include_spip('inc/noizetier_objet');
	return noizetier_objet_lire($objet, $id_objet, $information);
}


// Cette balise renvoie le tableau de la liste des objets possédant des noisettes configurées
function balise_NOIZETIER_OBJET_LISTE_dist($p) {

	// Aucun argument à la balise.
	$p->code = "calculer_liste_objets()";

	return $p;
}

function calculer_liste_objets() {

	include_spip('inc/noizetier_objet');
	return noizetier_objet_repertorier();
}

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
	include_spip('inc/noizetier_page');
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
