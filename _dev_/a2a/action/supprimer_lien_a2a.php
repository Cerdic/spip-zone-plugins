<?php

function action_supprimer_lien_a2a(){
	include_spip('inc/utils');
	include_spip('inc/headers');
	$contexte = array();
	$id_article_cible = _request('id_article_cible');
	$id_article = _request('id_article');
	
	sql_delete('spip_articles_lies',  array(
		'id_article = ' . sql_quote($id_article), 
		'id_article_lie = ' . sql_quote($id_article_cible)
		));
	
	include_spip('inc/header');
	redirige_par_entete(generer_url_ecrire("articles", "id_article=".$id_article, "&"));
}


?>
