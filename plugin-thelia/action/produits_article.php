<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function produits_article_update($id_article){
	//supprimer les enregistrements de cet article
	$query = "DELETE FROM spip_produits_articles WHERE id_article=" . _q($id_article);
	$result = spip_query($query);

	$query = "DELETE FROM spip_rubriquesthelia_articles WHERE id_article=" . _q($id_article);
	$result = spip_query($query);

	//ajouter les associations produits-articles de cet article
	foreach ($_POST as $clef => $valeur){
		if (strpos($clef, "produit-")===0){
			$id_produit = substr($clef, 8);
			spip_query("INSERT INTO spip_produits_articles (id_article,id_produit) VALUES (" . _q($id_article) . "," . _q($id_produit) . ")");
		}
	}

	//ajouter les associations rubriquesthelia-articles de cet article
	foreach ($_POST as $clef => $valeur){
		if (strpos($clef, "rubriquethelia-")===0){
			$id_rubriquethelia = substr($clef, 15);
			spip_query("INSERT INTO spip_rubriquesthelia_articles (id_article,id_rubriquethelia) VALUES (" . _q($id_article) . "," . _q($id_rubriquethelia) . ")");
		}
	}

	return array($id_article);
}

function action_produits_article(){

	global $auteur_session;
	$arg = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = _request('redirect');

	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	if (verifier_action_auteur("produits_article-$arg", $hash, $id_auteur)==TRUE){
		$arg = explode("-", $arg);
		$id_article = $arg[0];
		if (intval($id_article) && autoriser('modifier', 'article', $id_article)){
			list($id_article) = produits_article_update($id_article);
			//if ($redirect) $redirect = parametre_url($redirect,"id_article",$id_article);
		}
	}

	if ($redirect)
		redirige_par_entete(str_replace("&amp;", "&", urldecode($redirect)));

}

