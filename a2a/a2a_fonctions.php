<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function lister_articles_lies($id_article, $ordre){
	return sql_allfetsel('id_article_lie','spip_articles_lies','id_article=' . sql_quote($id_article),'',"rang $ordre");
}

function balise_ARTICLES_LIES($p) {
	$id_article = champ_sql('id_article', $p);
	$ordre = 'ASC';
	if($inverse = interprete_argument_balise(1,$p))
		$ordre = 'DESC';
	$p->code = "lister_articles_lies($id_article, $ordre)";
	$p->type = 'php';  
	return $p;
}

?>
