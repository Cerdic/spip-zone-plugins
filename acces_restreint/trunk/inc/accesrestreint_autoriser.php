<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/* pour que le pipeline ne rale pas ! */
function accesrestreint_autoriser() {
}

/**
 * Autorisation a administrer les zones
 *
 * @param unknown_type $faire
 * @param unknown_type $quoi
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $options
 * @return unknown
 */
function autoriser_zone_administrer($faire, $quoi, $id, $qui, $options) {
	// Les admins complets uniquement
	if ($qui['statut']=='0minirezo' and !$qui['restreint']) {
		return true;
	}

	return false;
}

/**
 * Autorisation a affecter les zones a un auteur
 * si un id_zone passe dans options, cela concerne plus particulierement le droit d'affecter cette zone
 *
 * @param unknown_type $faire
 * @param unknown_type $qui
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $options
 * @return unknown
 */
function autoriser_auteur_affecterzones_dist($faire, $quoi, $id, $qui, $options) {
	// Si on ne peut pas modifier l'auteur, c'est fichu
	if (!autoriser('modifier', 'auteur', $id)) {
		return false;
	}

	// Les admins complets
	if ($qui['statut'] == '0minirezo' and !$qui['restreint']) {
		return true;
	}

	// Les non-admins complets ne peuvent pas s'administrer eux-meme pour éviter les erreurs
	if ($id == $qui['id_auteur']) {
		return false;
	}

	// Si on parle d'une zone précise,
	// les non-admins complets ne peuvent affecter que les zones dont ils font partie
	include_spip('inc/accesrestreint');
	if ($options['id_zone']
		and accesrestreint_test_appartenance_zone_auteur($options['id_zone'], $qui['id_auteur'])
	) {
		return true;
	}

	return false;
}

/*
 * Surcharge (sans _dist) de la fonction d'autorisation de vue d'une rubrique, si pas déjà définie
 */
if (!function_exists('autoriser_rubrique_voir')) {
function autoriser_rubrique_voir($faire, $type, $id, $qui, $options) {
	include_spip('inc/accesrestreint');
	static $rub_exclues;

	$publique = isset($options['publique']) ? $options['publique'] : !test_espace_prive();
	$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];

	if (!isset($rub_exclues[$id_auteur][$publique]) || !is_array($rub_exclues[$id_auteur][$publique])) {
		$rub_exclues[$id_auteur][$publique] = accesrestreint_liste_rubriques_exclues($publique, $id_auteur);
		$rub_exclues[$id_auteur][$publique] = array_flip($rub_exclues[$id_auteur][$publique]);
	}

	return !isset($rub_exclues[$id_auteur][$publique][$id]);
}
}

/*
 * Surcharge (sans _dist) de la fonction d'autorisation de vue d'un article, si pas déjà définie
 */
if (!function_exists('autoriser_article_voir')) {
function autoriser_article_voir($faire, $type, $id, $qui, $options) {
	// Si on ne demande pas un article précis
	if (!$id) {
		// Les admins peuvent par défaut tout voir
		if ($qui['statut'] == '0minirezo') {
			return true;
		}
		// Les autres peuvent voir ce qui est publié ou proposé
		if (isset($options['statut'])) {
			$statut = $options['statut'];
			if (in_array($statut, array('prop', 'publie'))) {
				return true;
			}
		}
		return false;
	}

	include_spip('public/quete');
	include_spip('inc/accesrestreint');

	$publique = isset($options['publique']) ? $options['publique'] : !test_espace_prive();
	$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];

	// Si l'article fait partie des contenus restreints directement, c'est niet
	if (in_array($id, accesrestreint_liste_objets_exclus('articles', $publique, $id_auteur))) {
		return false;
	}

	// Si on ne connait pas déjà la rubrique, on la cherche suivant l'article
	if (!isset($options['id_rubrique']) or !$id_rubrique = $options['id_rubrique']) {
		$article = quete_parent_lang('spip_articles', $id);
		$id_rubrique = $article['id_rubrique'];
	}

	// On ne continue que si on peut déjà voir la rubrique parente
	if (autoriser_rubrique_voir('voir', 'rubrique', $id_rubrique, $qui, $options)) {
		// Si c'est bon, les admins peuvent tout voir
		if ($qui['statut'] == '0minirezo') {
			return true;
		}

		// Pour les autres, on ne peut pas voir un article 'prepa' ou 'poubelle' dont on n'est pas auteur
		$r = sql_getfetsel('statut', 'spip_articles', 'id_article='.sql_quote($id));
		include_spip('inc/auth'); // pour auteurs_article si espace public
		return
			in_array($r, array('prop', 'publie'))
			or auteurs_article($id, 'id_auteur='.$qui['id_auteur']);
	}

	return false;
}
}

/*
 * Surcharge (sans _dist) de la fonction d'autorisation de vue d'une brève, si pas déjà définie
 */
if (!function_exists('autoriser_breve_voir')) {
function autoriser_breve_voir($faire, $type, $id, $qui, $options) {
	include_spip('public/quete');
	include_spip('inc/accesrestreint');

	$publique = isset($options['publique']) ? $options['publique'] : !test_espace_prive();
	$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];

	// Si la brève fait partie des contenus restreints directement, c'est niet
	if (in_array($id, accesrestreint_liste_objets_exclus('breves', $publique, $id_auteur))) {
		return false;
	}

	if (!$id_rubrique = $options['id_rubrique']) {
		$breve = quete_parent_lang('spip_breves', $id);
		$id_rubrique = $breve['id_rubrique'];
	}

	return autoriser_rubrique_voir('voir', 'rubrique', $id_rubrique, $qui, $options);
}
}

/*
 * Surcharge (sans _dist) de la fonction d'autorisation de vue d'un site, si pas déjà définie
 */
if (!function_exists('autoriser_site_voir')) {
function autoriser_site_voir($faire, $type, $id, $qui, $options) {
	include_spip('public/quete');
	include_spip('inc/accesrestreint');

	$publique = isset($options['publique']) ? $options['publique'] : !test_espace_prive();
	$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];

	// Si le site fait partie des contenus restreints directement, c'est niet
	if (in_array($id, accesrestreint_liste_objets_exclus('sites', $publique, $id_auteur))) {
		return false;
	}

	if (!$id_rubrique = $options['id_rubrique']) {
		$site = quete_parent_lang('spip_syndic', $id);
		$id_rubrique = $site['id_rubrique'];
	}

	return autoriser_rubrique_voir('voir', 'rubrique', $id_rubrique, $qui, $options);
}
}

/*
 * Surcharge (sans _dist) de la fonction d'autorisation de vue d'un événement, si pas déjà définie
 */
if (!function_exists('autoriser_evenement_voir')) {
function autoriser_evenement_voir($faire, $type, $id, $qui, $options) {
	include_spip('inc/accesrestreint');
	static $evenements_statut;

	$publique = isset($options['publique']) ? $options['publique'] : !test_espace_prive();
	$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];

	// Si l'événement fait partie des contenus restreints directement, c'est niet
	if (in_array($id, accesrestreint_liste_objets_exclus('evenements', $publique, $id_auteur))) {
		return false;
	}

	if (!isset($evenements_statut[$id_auteur][$publique][$id])) {
		$id_article = sql_getfetsel('id_article', 'spip_evenements', 'id_evenement='.intval($id));
		$evenements_statut[$id_auteur][$publique][$id] = autoriser_article_voir('voir', 'article', $id_article, $qui, $options);
	}

	return $evenements_statut[$id_auteur][$publique][$id];
}
}

/*
 * Surcharge (sans _dist) de la fonction d'autorisation de vue d'un document, si pas déjà définie
 */
if (!function_exists('autoriser_document_voir')) {
function autoriser_document_voir($faire, $type, $id, $qui, $options) {
	include_spip('public/accesrestreint');
	include_spip('inc/accesrestreint');
	static $documents_statut = array();
	static $where = array();

	$publique = isset($options['publique']) ? $options['publique'] : !test_espace_prive();
	$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];

	// Si le document fait partie des contenus restreints directement, c'est niet
	if (in_array($id, accesrestreint_liste_objets_exclus('documents', $publique, $id_auteur))) {
		return false;
	}

	if (!isset($documents_statut[$id_auteur][$publique][$id])) {
		// il faut hacker la meta "creer_htaccess" le temps du calcul de l'autorisation car le core
		$clean_meta = false;
		if (isset($GLOBALS['meta']['accesrestreint_proteger_documents'])
			and $GLOBALS['meta']['accesrestreint_proteger_documents'] == 'oui'
		) {
			if (!isset($GLOBALS['meta']['creer_htaccess']) or $GLOBALS['meta']['creer_htaccess'] != 'oui') {
				$GLOBALS['meta']['creer_htaccess'] = 'oui';
				$clean_meta = true;
			}
		}

		if (!$id) {
			$documents_statut[$id_auteur][$publique][$id] = autoriser_document_voir_dist($faire, $type, $id, $qui, $options);
		} else {
			if (!isset($where[$publique])) {
				$where[$publique] = accesrestreint_documents_accessibles_where('id_document', $publique ? 'true' : 'false');
				// inclure avant le eval, pour que les fonctions soient bien definies
				include_spip('inc/accesrestreint');
				// eviter une notice sur $connect inexistant dans eval() qui suit
				$connect = '';

				$where[$publique] = eval('return '.$where[$publique].';');
			}

			$documents_statut[$id_auteur][$publique][$id] = sql_getfetsel('id_document', 'spip_documents', array('id_document='.intval($id),$where[$publique]));

			if ($documents_statut[$id_auteur][$publique][$id]) {
				$documents_statut[$id_auteur][$publique][$id] = autoriser_document_voir_dist($faire, $type, $id, $qui, $options);
			}
		}

		if ($clean_meta) {
			unset($GLOBALS['meta']['creer_htaccess']);
		}
	}

	return $documents_statut[$id_auteur][$publique][$id];
}
}
