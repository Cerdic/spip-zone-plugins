<?php
/**
 * Ce fichier contient les filtres et balises du noiZetier.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// --------------------------------------------------------------------------------
// --------------------- API TYPES DE NOISETTE : COMPLEMENT -----------------------
// --------------------------------------------------------------------------------


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
 * Compile la balise `#NOIZETIER_ICONE_LISTE` qui fournit la liste des icones d'une taille donnée en pixels
 * disponibles dans les thèmes SPIP de l'espace privé.
 * La signature de la balise est : `#NOIZETIER_ICONE_LISTE{taille}`.
 *
 * @package SPIP\NOIZETIER\ICONE\BALISE
 * @balise
 *
 * @example
 *     ```
 *     #NOIZETIER_BLOC_INFOS{24}, renvoie les icones de taille 24px présents dans les thèmes du privé
 *     ```
 *
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_NOIZETIER_ICONE_LISTE_dist($p) {
	$taille = interprete_argument_balise(1, $p);
	$taille = str_replace('\'', '"', $taille);
	$p->code = "calculer_liste_icones($taille)";

	return $p;
}

/**
 * @internal
 *
 * @param int $taille
 *
 * @return array|string
 */
function calculer_liste_icones($taille = 24) {

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
 * Compile la balise `#NOIZETIER_BLOC_INFOS` qui fournit un champ ou tous les champs descriptifs d'un bloc Z
 * donné. Ces champs sont lus dans le fichier YAML du bloc si il existe.
 * La signature de la balise est : `#NOIZETIER_BLOC_INFOS{bloc, information}`.
 *
 * @package SPIP\NOIZETIER\BLOC\BALISE
 * @balise
 *
 * @example
 *     ```
 *     #NOIZETIER_BLOC_INFOS{content}, renvoie tous les champs descriptifs du bloc content
 *     #NOIZETIER_BLOC_INFOS{content, nom}, renvoie le titre du bloc content
 *     ```
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_NOIZETIER_BLOC_INFOS_dist($p) {
	$bloc = interprete_argument_balise(1, $p);
	$bloc = str_replace('\'', '"', $bloc);
	$information = interprete_argument_balise(2, $p);
	$information = isset($information) ? str_replace('\'', '"', $information) : '""';
	$p->code = "calculer_infos_bloc($bloc, $information)";

	return $p;
}

/**
 * @internal
 *
 * @param string $bloc
 * @param string $information
 *
 * @return array|string
 */
function calculer_infos_bloc($bloc = '', $information = '') {

	include_spip('inc/noizetier_bloc');
	return noizetier_bloc_lire($bloc, $information);
}


// -------------------------------------------------------------------
// ---------------------------- API PAGES ----------------------------
// -------------------------------------------------------------------

/**
 * Compile la balise `#NOIZETIER_PAGE_INFOS` qui fournit un champ ou tous les champs descriptifs d'une page
 * ou d'une composition donnée. Ces champs sont lus dans la table `spip_noizetier_pages`.
 * La signature de la balise est : `#NOIZETIER_PAGE_INFOS{page, information}`.
 *
 * La fonction peut aussi renvoyer d'autres informations calculées, à savoir :
 * - `est_modifiee` qui indique si la configuration du fichier YAML ou XML de la page a été modifiée ou pas.
 * - `compteurs_type_noisette` qui donne le nombre de types de noisettes disponibles pour la page ou la composition
 *    donnée en distinguant les types de noisettes communs à toutes les pages, les types de noisettes spécifiques à
 *    un type de page et les types de noisettes spécifiques à une composition.
 * - `compteurs_noisette` qui donne le nombre de noisettes incluses dans chaque bloc de la page.
 *
 * @package SPIP\NOIZETIER\PAGE\BALISE
 * @balise
 *
 * @example
 *     ```
 *     #NOIZETIER_PAGE_INFOS{article}, renvoie tous les champs descriptifs de la page article
 *     #NOIZETIER_PAGE_INFOS{article, nom}, renvoie le titre de la page article
 *     #NOIZETIER_PAGE_INFOS{article-forum, nom}, renvoie le titre de la composition forum de la page article
 *     #NOIZETIER_PAGE_INFOS{article, est_modifiee}, indique si la configuration de la page article a été modifiée
 *     #NOIZETIER_PAGE_INFOS{article, compteurs_type_noisette}, fournit les compteurs de types de noisette compatibles
 *     #NOIZETIER_PAGE_INFOS{article, compteurs_noisette}, fournit les compteurs de noisettes incluses par bloc
 *     ```
 *
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_NOIZETIER_PAGE_INFOS_dist($p) {

	// Récupération des arguments de la balise.
	// -- seul l'argument information est optionnel.
	$page = interprete_argument_balise(1, $p);
	$page = str_replace('\'', '"', $page);
	$information = interprete_argument_balise(2, $p);
	$information = isset($information) ? str_replace('\'', '"', $information) : '""';

	// Calcul de la balise
	$p->code = "calculer_infos_page($page, $information)";

	return $p;
}

/**
 * @internal
 *
 * @param        $page
 * @param string $information
 *
 * @return mixed
 */
function calculer_infos_page($page, $information = '') {

	include_spip('inc/noizetier_page');
	if ($information == 'est_modifiee') {
		// Initialisation du retour
		$retour = true;

		// Détermination du répertoire par défaut
		$repertoire = noizetier_page_initialiser_dossier();

		// Récupération du md5 enregistré en base de données
		$from = 'spip_noizetier_pages';
		$where = array('page=' . sql_quote($page));
		$md5_enregistre = sql_getfetsel('signature', $from, $where);

		if ($md5_enregistre) {
			// On recherche d'abord le fichier YAML et sinon le fichier XML pour la compatibilité ascendante.
			if (($fichier = find_in_path("${repertoire}${page}.yaml"))
			or ($fichier = find_in_path("${repertoire}${page}.xml"))) {
				$md5 = md5_file($fichier);
				if ($md5 == $md5_enregistre) {
					$retour = false;
				}
			}
		}
	} elseif ($information == 'compteurs_type_noisette') {
		// Initialisation des compteurs par bloc
		$retour = array(
			'composition' => 0,
			'type'        => 0,
			'commun'      => 0
		);

		// Acquisition du type et de la composition éventuelle.
		$type = noizetier_page_extraire_type($page);
		$composition = noizetier_page_extraire_composition($page);

		// Les compteurs de types de noisette d'une page sont calculés par une lecture de la table 'spip_types_noisettes'.
		$from = array('spip_types_noisettes');
		$where = array(
			'plugin=' . sql_quote('noizetier'),
			'type=' . sql_quote($type),
			'composition=' . sql_quote($composition)
		);
		$compteur = sql_countsel($from, $where);

		// On cherche maintenant les 3 compteurs possibles :
		if ($composition) {
			// - les types de noisette spécifiques de la composition si la page en est une.
			if ($compteur) {
				$retour['composition'] = $compteur;
			}
			$where[2] = 'composition=' . sql_quote('');
			$compteur = sql_countsel($from, $where);
			if ($compteur) {
				$retour['type'] = $compteur;
			}
		} else {
			// - les types de noisette spécifiques de la page ou du type de la composition
			if ($compteur) {
				$retour['type'] = $compteur;
			}
		}
		// - les types de noisette communs à toutes les pages.
		$where[1] = 'type=' . sql_quote('');
		$compteur = sql_countsel($from, $where);
		if ($compteur) {
			$retour['commun'] = $compteur;
		}

		$retour['total'] = array_sum($retour);
	} elseif ($information == 'compteurs_noisette') {
		$retour = noizetier_page_compter_noisettes($page);
	} else {
		$retour = noizetier_page_lire($page, $information);
	}

	return $retour;
}


// --------------------------------------------------------------------
// ---------------------------- API OBJETS ----------------------------
// --------------------------------------------------------------------

/**
 * Compile la balise `#NOIZETIER_OBJET_INFOS` qui fournit un champ ou tous les champs descriptifs d'un objet
 * donné. Ces champs sont lus dans la table de l'objet.
 * La signature de la balise est : `#NOIZETIER_OBJET_INFOS{type_objet, id_objet, information}`.
 *
 * La fonction peut aussi renvoyer d'autres informations calculées, à savoir :
 * - `compteurs_noisette` qui donne le nombre de noisettes incluses dans chaque bloc de l'objet.
 *
 * @package SPIP\NOIZETIER\OBJET\BALISE
 * @balise
 *
 * @example
 *     ```
 *     #NOIZETIER_OBJET_INFOS{article, 12}, renvoie tous les champs descriptifs de la page article
 *     #NOIZETIER_OBJET_INFOS{article, 12, nom}, renvoie le titre de la page article
 *     #NOIZETIER_OBJET_INFOS{article, 12, compteurs_noisette}, fournit les compteurs de noisettes incluses par bloc
 *     ```
 *
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
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

/**
 * @internal
 *
 * @param        $objet
 * @param        $id_objet
 * @param string $information
 *
 * @return mixed
 */
function calculer_infos_objet($objet, $id_objet, $information = '') {

	include_spip('inc/noizetier_objet');
	if ($information == 'compteurs_noisette') {
		$retour = noizetier_objet_compter_noisettes($objet, $id_objet);
	} else {
		$retour = noizetier_objet_lire($objet, $id_objet, $information);
	}
	return $retour;
}


/**
 * Compile la balise `#NOIZETIER_OBJET_LISTE` qui renvoie la liste des objets possédant des noisettes
 * configurées. Chaque objet est fourni avec sa description complète.
 * La signature de la balise est : `#NOIZETIER_OBJET_LISTE`.
 *
 * @balise
 *
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_NOIZETIER_OBJET_LISTE_dist($p) {

	// Aucun argument à la balise.
	$p->code = "calculer_liste_objets()";

	return $p;
}

/**
 * @internal
 *
 * @return array|string
 */
function calculer_liste_objets() {

	include_spip('inc/noizetier_objet');
	return noizetier_objet_repertorier();
}
