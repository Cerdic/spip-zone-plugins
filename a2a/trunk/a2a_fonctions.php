<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function a2a_traduire_type_liaisons($type){
	$types_liaisons = lister_types_liaisons();
	return _T($types_liaisons[$type]);	
}
function lister_articles_lies($id_article, $ordre,$type_liaison=null){
	if ($type_liaison==null)
		return sql_allfetsel('id_article_lie','spip_articles_lies','id_article=' . sql_quote($id_article),'',"rang $ordre");
	else
		return sql_allfetsel('id_article_lie','spip_articles_lies','id_article=' . sql_quote($id_article) . ' AND type_liaison=' . sql_quote($type_liaison),'',"rang $ordre");
}

function balise_ARTICLES_LIES($p) { 
	$id_article = champ_sql('id_article', $p);
	$ordre = 'ASC';
	$type_liaison=interprete_argument_balise(2,$p);
	if($inverse = interprete_argument_balise(1,$p))
		$ordre = 'DESC';
	$type_liaison ? $p->code = "lister_articles_lies($id_article, $ordre,$type_liaison)" : $p->code = "lister_articles_lies($id_article, $ordre)";
	$p->type = 'php';  
	return $p;
}

function lister_articles_liant($id_article,$ordre,$type_liaison=null){
	if ($type_liaison==null)
	    return sql_allfetsel('id_article','spip_articles_lies','id_article_lie=' . sql_quote($id_article),'',"rang $ordre");
	else
	    return sql_allfetsel('id_article','spip_articles_lies','id_article_lie=' . sql_quote($id_article) . ' AND type_liaison=' . sql_quote($type_liaison),'',"rang $ordre");
	}

function balise_ARTICLES_LIANT($p) { 
	$id_article = champ_sql('id_article', $p);
	$ordre = 'ASC';
	$type_liaison=interprete_argument_balise(2,$p);
	if($inverse = interprete_argument_balise(1,$p))
		$ordre = 'DESC';
	$type_liaison ? $p->code = "lister_articles_liant($id_article,$ordre,$type_liaison)" : $p->code = "lister_articles_liant($id_article,$ordre)";
	$p->type = 'php';  
	return $p;
}

function types_liaisons_existent($array){
    // return ' ' si des liaisons existent, sinon retourne ''
    if (empty($array) or $array==array(''=>'')){
        return '';    
    }   
    return ' ';
}
    
function lister_types_liaisons(){
	// fournit tout les types de liaisons ita est : define + cfg.
	include_spip('inc/config');
	if ($GLOBALS['a2a_types_liaisons'])
		$types_liaisons = array_merge(lire_config('a2a/types_liaisons'),$GLOBALS['a2a_types_liaisons']);	
	else 
		$types_liaisons = lire_config('a2a/types_liaisons');
	asort($types_liaisons);
	return $types_liaisons;
}
function balise_TYPES_LIAISONS($p){
	$p->code = "lister_types_liaisons()";
	return $p;	
}

?>