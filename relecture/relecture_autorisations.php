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

	// Conditions :
	// - l'auteur connecte est un des auteurs de l'article
	// - l'article est dans l'état "en cours de rédaction"
	// - l'article n'a pas deja une relecture d'ouverte

	$les_auteurs = lister_objets_lies('auteur', 'article', $id, 'auteurs_liens');

	$from = 'spip_articles';
	$where = array("id_article=$id");
	$statut = sql_getfetsel('statut', $from, $where);

	$from = 'spip_relectures';
	$where = array("id_article=$id", "statut=" . sql_quote('ouverte'));
	$nb_relecture_ouverte = sql_countsel($from, $where);

	return
		(in_array($qui['id_auteur'], $les_auteurs)
		AND ($statut=='prepa')
		AND ($nb_relecture_ouverte==0));
}


/**
 * Autorisation de consultations des relectures cloturees d'un article
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_article_voirrelectures_dist($faire, $type, $id, $qui, $opt) {

	// Conditions :
	// - pour l'instant tout le monde peut afficher les fiches de relecture clôturées

	return true;
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

	// Conditions :
	// - l'auteur connecte est un des auteurs de l'article
	// - ou un admin complet ou restreint à la rubrique d'appartenance de l'article

	$from = 'spip_relectures';
	$where = array("id_relecture=$id");
	$id_article = sql_getfetsel('id_article', $from, $where);
	$les_auteurs = lister_objets_lies('auteur', 'article', $id_article, 'auteurs_liens');

	return
		(in_array($qui['id_auteur'], $les_auteurs)
		OR ($qui['statut'] == '0minirezo'));
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

	// Conditions :
	// - l'auteur connecte est un des auteurs ou des relecteurs de l'article

	$from = 'spip_relectures';
	$where = array("id_relecture=$id");
	$infos = sql_fetsel('id_article, relecteurs', $from, $where);
	$les_relecteurs = unserialize($infos['relecteurs']);

	$les_auteurs = lister_objets_lies('auteur', 'article', $infos['id_article'], 'auteurs_liens');

	return
		(in_array($qui['id_auteur'], $les_auteurs)
		OR in_array($qui['id_auteur'], $les_relecteurs));

}


/**
 * Autorisation d'affichage d'une relecture en cours
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_relecture_voir_dist($faire, $type, $id, $qui, $opt) {

	// Conditions :
	// - l'auteur connecte est un des auteurs ou des relecteurs de l'article

	$from = 'spip_relectures';
	$where = array("id_relecture=$id");
	$infos = sql_fetsel('id_article, relecteurs', $from, $where);
	$les_relecteurs = unserialize($infos['relecteurs']);

	$les_auteurs = lister_objets_lies('auteur', 'article', $infos['id_article'], 'auteurs_liens');

	return
		(in_array($qui['id_auteur'], $les_auteurs)
		OR in_array($qui['id_auteur'], $les_relecteurs));

}

?>
