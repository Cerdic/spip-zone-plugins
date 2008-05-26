<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_a2a_lier_article(){
	$id_article_cible = _request('id_article');
	$id_article_source = _request('id_article_orig');
	//on verifie que cet article n'est pas deja lie
	if (!sql_countsel('spip_articles_lies', array(
		'id_article=' . sql_quote($id_article_source),
		'id_article_lie=' . sql_quote($id_article_cible)))){
			//on recupere le rang le plus haut pour definir celui de l'article a lier
			$rang = sql_getfetsel('MAX(rang)', 'spip_articles_lies', 'id_article='. sql_quote($id_article_source));
			//on ajoute le lien vers l'article
			sql_insertq('spip_articles_lies', array(
				'id_article' => $id_article_source,
				'id_article_lie' => $id_article_cible,
				'rang' => ++$rang
				));
	}

	include_spip('inc/header');
	redirige_par_entete(generer_url_ecrire("articles", "id_article=".$id_article_source, "&"));
}

?>
