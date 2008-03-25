<?php

function lister_articles_lies($id_article){
	$liste = '';
	$result = spip_query("SELECT id_article_lie FROM spip_articles_lies WHERE id_article=$id_article");
	while($row = spip_fetch_array($result)) {
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
