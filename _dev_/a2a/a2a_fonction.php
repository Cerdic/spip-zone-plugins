<?php

function lister_articles_lies($id_article){
	$liste = '';
	$result = sql_select('id_article_lie','spip_articles_lies','id_article=' . sql_quote($id_article));
	while($row = sql_fetch($result)) {
		$liste[] = $row['id_article_lie'];	
	}
	return $liste;
}
function balise_ARTICLES_LIES($p) {
	$id_article = champ_sql('id_article', $p);
	$p->code = "lister_articles_lies($id_article)";
	$p->type = 'php';  
	return $p;
}
?>
