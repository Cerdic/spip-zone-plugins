<?php

function lister_articles_lies($id_article){
	return sql_allfetsel('id_article_lie','spip_articles_lies','id_article=' . sql_quote($id_article));
}

function balise_ARTICLES_LIES($p) {
	$id_article = champ_sql('id_article', $p);
	$p->code = "lister_articles_lies($id_article)";
	$p->type = 'php';  
	return $p;
}
?>
