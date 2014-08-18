<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function lister_articles_selectionnes($id_rubrique,$ordre='ASC'){
	return sql_allfetsel('id_article','spip_pb_selection','id_rubrique=' . sql_quote($id_rubrique),'',"ordre $ordre");
}

function balise_SELECTION_ARTICLES($p) { 
	$id_rubrique = champ_sql('id_rubrique', $p);
	$p->code = "lister_articles_selectionnes($id_rubrique)";
	$p->type = 'php';  
	return $p;
}
function balise_SELECTION_ARTICLE($p){
	return balise_SELECTION_ARTICLES($p);
}