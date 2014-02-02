<?php
/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function relecture_autoriser() {}


/**
 * Autorisation d'ouverture d'une relecture
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_article_ouvrirrelecture_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// - l'auteur connecte possède l'autorisation de modifier l'article
	// - l'article est dans l'état "proposé à l'évaluation"
	// - l'article n'a pas deja une relecture d'ouverte
	if ($id_article = intval($id)) {
		$auteur_autorise = autoriser('modifier', 'article', $id_article, $qui);

		$from = 'spip_articles';
		$where = array("id_article=$id_article");
		$statut = sql_getfetsel('statut', $from, $where);

		$from = 'spip_relectures';
		$where = array("id_article=$id_article", "statut=" . sql_quote('ouverte'));
		$nb_relecture_ouverte = intval(sql_countsel($from, $where));

		$autoriser =
			($auteur_autorise
			AND ($statut=='prop')
			AND ($nb_relecture_ouverte==0));
	}

	return $autoriser;
}


/**
 * Autorisation de consultation des relectures cloturees d'un article ou les informations
 * sur la relecture en cours
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_article_voirrelectures_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// - pour l'instant tout le monde peut afficher les fiches de relecture clôturées
	if ($id_article = intval($id)) {
		$autoriser = true;
	}

	return $autoriser;
}


/**
 * Autorisation de modification d'une relecture
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_relecture_modifier_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// - l'auteur connecte possède l'autorisation de modifier l'article
	// - la relecture n'est pas fermee
	if ($id_relecture = intval($id)) {
		$auteur_autorise = autoriser('modifier', 'article', $id_article, $qui);

		$from = 'spip_relectures';
		$where = array("id_relecture=$id_relecture");
		$infos = sql_fetsel('id_article, statut', $from, $where);

		$relecture_ouverte = ($infos['statut'] == 'ouverte');

		$autoriser =
			($auteur_autorise
			AND $relecture_ouverte);
	}

	return $autoriser;
}


/**
 * Autorisation de modifier le statut d'un commentaire
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_relecture_instituer_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	return $autoriser;
}


/**
 * Autorisation de deposer des commentaires sur la relecture
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_relecture_commenter_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// - l'auteur connecte est un des auteurs ou des relecteurs de l'article
	// - la periode de relecture ne doit pas etre echue

	if ($id_relecture = intval($id)) {
		$from = 'spip_relectures';
		$where = array("id_relecture=$id_relecture");
		$infos = sql_fetsel('id_article, date_fin_commentaire', $from, $where);

		$les_relecteurs = lister_objets_lies('auteur', 'relecture', $id, 'auteurs_liens');
		$les_auteurs = lister_objets_lies('auteur', 'article', $infos['id_article'], 'auteurs_liens');

		$autoriser =
			(strtotime($infos['date_fin_commentaire'])>time()
			AND (in_array($qui['id_auteur'], $les_auteurs)
				OR in_array($qui['id_auteur'], $les_relecteurs)));
	}

	return $autoriser;
}


/**
 * Autorisation de modifier le texte d'un commentaire
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_commentaire_modifier_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// - Seul l'auteur ayant depose le commmentaire peut le modifier
	// - le commentaire est encore ouvert

	if ($id_commentaire = intval($id)) {
		$from = 'spip_commentaires';
		$where = array("id_commentaire=$id_commentaire");
		$infos = sql_fetsel('id_emetteur, statut', $from, $where);

		$autoriser =
			(($qui['id_auteur'] == $infos['id_emetteur'])
			AND ($infos['statut'] == 'ouvert'));
	}

	return $autoriser;
}


/**
 * Autorisation moderer - repondre, changer le statut, supprimer - un commentaire
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_commentaire_moderer_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// - l'auteur connecte est un des auteurs de l'article
	// - ou un admin complet ou restreint à la rubrique d'appartenance de l'article (besoin de maintenance)
	// - le commentaire est encore ouvert

	if ($id_commentaire = intval($id)) {
		$from = array('spip_commentaires AS c', 'spip_relectures AS r');
		$where = array("id_commentaire=$id_commentaire", 'c.id_relecture=r.id_relecture');
		$infos = sql_fetsel('c.statut, r.id_article', $from, $where);

		$id_article = $infos['id_article'];
		$les_auteurs = lister_objets_lies('auteur', 'article', $id_article, 'auteurs_liens');

		$from = 'spip_articles';
		$where = array("id_article=$id_article");
		$id_rubrique = sql_getfetsel('id_rubrique', $from, $where);

		$autoriser =
			(($infos['statut'] == 'ouvert')
			AND
			((in_array($qui['id_auteur'], $les_auteurs)
				OR (($qui['statut'] == '0minirezo')
					AND (!$qui['restreint'] OR !$id_rubrique OR in_array($id_rubrique, $qui['restreint']))))));
	}

	return $autoriser;
}


/**
 * Autorisation de modifier le statut d'un commentaire
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_commentaire_instituer_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = true;

	return $autoriser;
}


?>
