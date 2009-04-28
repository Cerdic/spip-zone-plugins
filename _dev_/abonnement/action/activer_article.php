<?php

/**
 * Plugin Abonnement pour Spip 2.0
 * Licence GPL (c) 2009
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');


function action_activer_article_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$args = $securiser_action();

	// id_article-id_auteur-hash_article
	$args = explode('-',$args);
	
	if (count($args)!=2) {
		spip_log("action_activer_article_dist pas compris");
		die("action_activer_article_dist pas compris");
	}
	
	return abo_traiter_activer_article(intval($args[0]), intval($args[1]));

}


function abo_traiter_activer_article($id_article, $id_auteur) {
	
	// article non trouve ?
	$article = sql_getfetsel('id_article', 'spip_articles', 'id_article = ' . $id_article);
	if (!$article) {
		spip_log("abonnement article $id_article inexistant");
		die("abonnement article $id_article inexistant");
	}

	// article deja cree ?
	if (!$id = sql_getfetsel('id_article',"spip_auteurs_elargis_articles",array("id_auteur"=>$id_auteur, "id_article"=>$id_article))) {
		// on en cree un
		sql_insertq("spip_auteurs_elargis_articles", array(
			"id_auteur"=>$id_auteur,
			"id_article"=>$id_article,
			'statut_paiement'=>'ok'
		));
	}
	// sinon on met a jour
	else {
		sql_updateq(
			"spip_auteurs_elargis_articles",
			array('statut_paiement'=>'ok'),
			array("id_auteur=".$id_auteur, "id_article=".$id_article));
	}

	return true;
}

?>
