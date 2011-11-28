<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Surcharge de inc_article_select_dist afin de déterminer correctement la rubrique initiale si elle est nulle
 * Appelle inc_article_select_dist en ayant renseigné $id_rubrique avec l'id de la rubrique preferee si c'est renseigné
 * sinon la premiere rubrique que l'auteur administre (donc dans laquelle il peut publier)
 */
function inc_article_select($id_article, $id_rubrique=0, $lier_trad=0, $id_version=0) {

	// Si nouvel article et pas de rubrique
	if (!is_numeric($id_article) && !$id_rubrique) {
	   $qui = $GLOBALS['visiteur_session'] ? $GLOBALS['visiteur_session'] : array('statut' => '', 'id_auteur' =>0, 'webmestre' => 'non');
		include_spip('inc/autoriser');
		$qui['restreint'] = liste_rubriques_auteur($qui['id_auteur']);

		$res = sql_select("rubrique_preferee", "spip_auteurs", "id_auteur=".$qui['id_auteur']);
		$id_rubrique = reset(picker_selected(sql_fetch($res),"rubrique"));
		$id_rubrique = $id_rubrique ? $id_rubrique : reset($qui['restreint']);
	}

	include_spip('inc/article_select');
	return(inc_article_select_dist($id_article, $id_rubrique, $lier_trad, $id_version));
}


?>
