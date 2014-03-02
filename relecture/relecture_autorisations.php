<?php
/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function relecture_autoriser() {}


/* ----------------------- AUTORISATIONS DE L'OBJET ARTICLE ----------------------- */

/**
 * Autorisation d'ouverture d'une relecture sur un article
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
	// - l'auteur connecté possède l'autorisation de modifier l'article
	// - l'article est dans l'état "proposé à l'évaluation"
	// - l'article n'a pas déjà une relecture d'ouverte
	// - l'article possède au moins un élément textuel non vide
	if ($id_article = intval($id)) {
		$auteur_autorise = autoriser('modifier', 'article', $id_article, $qui, $opt);

		$from = 'spip_articles';
		$where = array("id_article=$id_article");
		$infos = sql_fetsel('statut,chapo,descriptif,texte,ps', $from, $where);

		$from = 'spip_relectures';
		$where = array("id_article=$id_article", "statut=" . sql_quote('ouverte'));
		$nb_relecture_ouverte = intval(sql_countsel($from, $where));

		$taille_elements = strlen($infos['chapo'])
						 + strlen($infos['descriptif'])
						 + strlen($infos['texte'])
						 + strlen($infos['ps']);

		$autoriser =
			($auteur_autorise
			AND ($infos['statut']=='prop')
			AND ($nb_relecture_ouverte==0)
			AND ($taille_elements > 0));
	}

	return $autoriser;
}


/**
 * Autorisation de consultation des relectures cloturees d'un article ou des informations
 * sur une relecture ouverte
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
	// - l'auteur connecté possède l'autorisation de voir l'article
	//   (pour éviter de voir une relecture d'un article interdit).
	if ($id_article = intval($id)) {
		$autoriser = autoriser('voir', 'article', $id_article, $qui, $opt);
	}

	return $autoriser;
}


/* ----------------------- AUTORISATIONS DE L'OBJET RELECTURE ----------------------- */

/**
 * Autorisation de modification des informations concernant une relecture
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
	// - la relecture est ouverte
	if ($id_relecture = intval($id)) {
		$from = 'spip_relectures';
		$where = array("id_relecture=$id_relecture");
		$infos = sql_fetsel('id_article, statut', $from, $where);

		$relecture_ouverte = ($infos['statut'] == 'ouverte');

		$auteur_autorise = autoriser('modifier', 'article', $infos['id_article'], $qui, $opt);

		$autoriser =
			($auteur_autorise
			AND $relecture_ouverte);
	}

	return $autoriser;
}


/**
 * Autorisation d'accéder à la page des informations sur une relecture ouverte ou cloturée
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_relecture_voir_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// soit,
	// - la relecture est ouverte
	// - l'auteur connecté possède l'autorisation de modifier ou de commenter la relecture
	// ou soit,
	// - la relecture est clôturée
	// - et l'auteur connecté possède l'autorisation de voir les relectures de l'article
	if ($id_relecture = intval($id)) {
		$from = 'spip_relectures';
		$where = array("id_relecture=$id_relecture");
		$infos = sql_fetsel('id_article, statut', $from, $where);

		$relecture_ouverte = ($infos['statut'] == 'ouverte');
		if ($relecture_ouverte) {
			$autoriser =
				(autoriser('modifier', 'relecture', $id_relecture, $qui, $opt)
				OR autoriser('commenter', 'relecture', $id_relecture, $qui, $opt));
		}
		else {
			$autoriser = autoriser('voirrelectures', 'article', $infos['id_article'], $qui, $opt);
		}
	}

	return $autoriser;
}


/**
 * Autorisation de déposer des commentaires dans une relecture ouverte
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
	// soit,
	// - la relecture est restreinte (il existe une liste des relecteurs)
	// - l'auteur connecté possède l'autorisation de modifier l'article ou est un relecteur de l'article
	// - la période de relecture n'est pas échue
	// ou soit,
	// - la relecture est ouverte à tous les rédacteurs
	// - l'auteur connecté possède l'autorisation de modifier l'article ou est un rédacteur du site
	// - la période de relecture n'est pas échue
	if ($id_relecture = intval($id)) {
		$from = 'spip_relectures';
		$where = array("id_relecture=$id_relecture");
		$infos = sql_fetsel('id_article, date_fin_commentaire, restreinte', $from, $where);

		$relecture_restreinte = ($infos['restreinte'] == 'oui');
		$periode_non_echue = strtotime($infos['date_fin_commentaire'])>time();
		$autorise_modifier_article = autoriser('modifier', 'article', $infos['id_article'], $qui, $opt);

		if ($relecture_restreinte) {
			$les_relecteurs = lister_objets_lies('auteur', 'relecture', $id, 'auteurs_liens');
			$autoriser =
				($periode_non_echue
				AND ($autorise_modifier_article
					OR in_array($qui['id_auteur'], $les_relecteurs)));
		}
		else {
			$autoriser =
				($periode_non_echue
				AND ($autorise_modifier_article
					OR ($qui['statut'] == '1comite')));
		}
	}

	return $autoriser;
}


/**
 * Autorisation de modifier le statut d'une relecture ouverte
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

	// Conditions :
	// soit,
	// - des commentaires ont été déposés
	//   et plus aucun commentaire n'est encore ouvert (la période peut-être échue ou pas)
	// - la relecture est ouverte
	// - l'auteur connecté possède l'autorisation de modifier la relecture
	// ou soit,
	// - aucun commentaire n'a encore été déposé
	//   et la période de dépose des commentaires est échue
	// - la relecture est ouverte
	// - l'auteur connecté possède l'autorisation de modifier la relecture
	if ($id_relecture = intval($id)) {
		$from = 'spip_relectures';
		$where = array("id_relecture=$id_relecture");
		$infos = sql_fetsel('statut, date_fin_commentaire', $from, $where);
		$relecture_ouverte = ($infos['statut'] == 'ouverte');
		$periode_echue = strtotime($infos['date_fin_commentaire'])<=time();

		$from = 'spip_commentaires';
		$where = array("id_relecture=$id_relecture");
		$nb_commentaires = intval(sql_countsel($from, $where));
		$where[] = "statut=" . sql_quote('ouvert');
		$nb_commentaires_ouverts = intval(sql_countsel($from, $where));

		$autorise_modifier_relecture = autoriser('modifier', 'relecture', $id_relecture, $qui, $opt);

		$autoriser =
			($relecture_ouverte
			AND $autorise_modifier_relecture
			AND ((($nb_commentaires==0)	AND $periode_echue)
				OR (($nb_commentaires>0) AND ($nb_commentaires_ouverts==0))
				));
	}

	return $autoriser;
}


/* ----------------------- AUTORISATIONS DE L'OBJET COMMENTAIRE ----------------------- */

/**
 * Autorisation de voir le texte, la réponse et les message du forum d'un commentaire
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_commentaire_voir_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// soit,
	// - le commentaire est supprimé,
	// - et seul son auteur peut encore y accéder
	// soit,
	// - le commentaire est dans un autre statut
	// et l'auteur connecté possède l'autorisation de voir la relecture
	if ($id_commentaire = intval($id)) {
		$from = 'spip_commentaires';
		$where = array("id_commentaire=$id_commentaire");
		$infos = sql_fetsel('id_relecture, id_emetteur, statut', $from, $where);

		$commentaire_supprime = ($infos['statut'] == 'poubelle');

		$autorise_voir_relecture = autoriser('voir', 'relecture', intval($infos['id_relecture']),$qui, $opt);

		$autoriser =
			(($commentaire_supprime AND ($qui['id_auteur'] == $infos['id_emetteur']))
			OR (!$commentaire_supprime AND $autorise_voir_relecture));
	}

	return $autoriser;
}

/**
 * Autorisation de modifier le texte ou la réponse d'un commentaire.
 * L'élément à modifier (texte ou réponse) est passé dans l'argument $opt['champ']
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
	// soit,
	// - l'auteur concerné est l'auteur du commmentaire (-> il peut donc modifier le texte)
	// - la modification concerne le "texte" du commentaire ou n'est pas précisée
	// - le commentaire est ouvert
	// - aucun message de forum n'a encore été déposé sur le commentaire
	// soit,
	// - l'auteur possède l'autorisation de modifier la relecture (-> il peut donc modifier la réponse)
	// - la modification concerne la "réponse" du commentaire ou n'est pas précisé
	// - le commentaire est ouvert
	if ($id_commentaire = intval($id)) {
		$from = 'spip_commentaires';
		$where = array("id_commentaire=$id_commentaire");
		$infos = sql_fetsel('id_emetteur, statut, id_relecture', $from, $where);

		$commentaire_ouvert = ($infos['statut'] == 'ouvert');

		$nb_messages_forum = sql_countsel('spip_forums', array("id_commentaire=$id_commentaire"));

		$autorise_modifier_relecture = autoriser('modifier', 'relecture', intval($infos['id_relecture']),$qui, array());

		$autoriser =
			((($qui['id_auteur'] == $infos['id_emetteur'])
				AND $commentaire_ouvert
				AND ($nb_messages_forum == 0)
				AND (!$opt OR ($opt['champ'] == 'texte')))
			OR ($autorise_modifier_relecture
				AND $commentaire_ouvert
				AND (!$opt OR ($opt['champ'] == 'texte'))));
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
function autoriser_commentaire_participerforum_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// - Seul l'auteur ayant depose le commmentaire peut le modifier
	// - le commentaire est encore ouvert
	if ($id_commentaire = intval($id)) {
		$from = 'spip_commentaires';
		$where = array("id_commentaire=$id_commentaire");
		$infos = sql_fetsel('statut', $from, $where);

		$commentaire_ouvert = ($infos['statut'] == 'ouvert');

		$autorise_voir_commentaire = autoriser('voir', 'commentaire', $id_commentaire, $qui, $opt);

		$autoriser =
			($commentaire_ouvert AND $autorise_voir_commentaire);
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

	$autoriser = false;
	$autoriser = true;

	return $autoriser;
}

?>
