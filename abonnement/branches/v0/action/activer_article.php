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

function abo_traiter_activer_article_hash($hash) {
	return abo_traiter_activer_article(0, 0, $hash);
}

/* on passe soit
 * - id_abonnement + id_auteur
 * - hash
 */
function abo_traiter_activer_article($id_article, $id_auteur, $hash='') {

	// si hash on le retrouve
	// s'il n'est pas la, on se tue.
	if ($hash) {
		if (!$abonnement_auteur_article = sql_fetsel('*', 'spip_auteurs_elargis_articles', 'hash = ' . sql_quote($hash))) {
			return false;
		}
		$id_article = $abonnement_auteur_article['id_article'];
		$id_auteur = $abonnement_auteur_article['id_auteur_elargi'];
	}
		
	// article non trouve ?
	$article = sql_getfetsel('id_article', 'spip_articles', 'id_article = ' . $id_article);
	if (!$article) {
		spip_log("abonnement article $id_article inexistant");
		die("abonnement article $id_article inexistant");
	}

	// S'il y a un hash de verification
	// (on provient alors certainement d'un formulaire de paiement)
	// et qu'il n'existe pas dans la base, on s'en va !
	$where = array("id_auteur_elargi=".$id_auteur, "id_article=".$id_article);
	if ($hash) $where["hash"] = sql_quote($hash);
	
	// article deja cree ?
	if (!$id = sql_getfetsel('id_article', "spip_auteurs_elargis_articles", $where)) {
		// si hash, c'est qu'on a pas trouve la valeur : dehors !
		if ($hash) {
			return false;
		}
		
		// sinon on en cree un
		sql_insertq("spip_auteurs_elargis_articles", array(
			"id_auteur_elargi"=>$id_auteur,
			"id_article"=>$id_article,
			'date' => date('Y-m-d H:i:s'),
			'statut_paiement'=>'ok',
			'montant'=>lire_config('abonnement/prix_article'),
		));
	}
	// sinon on met a jour
	else {
		sql_updateq(
			"spip_auteurs_elargis_articles",
			array(
				'statut_paiement' => 'ok',
				'date' => date('Y-m-d H:i:s'),
				'montant'=>lire_config('abonnement/prix_article'),
			),
			$where);
	}
	
	// signaler un changement
	spip_log("abonnement: activation article n°$id_article pour auteur $id_auteur","abonnement");
	
	return true;
}

?>
