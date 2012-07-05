<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function numero_denumerote_titre($titre){
	return preg_replace(',^([0-9]+[.]\s+),','',$titre);
}

function numero_numeroter_rubrique($id_rubrique,$type='rubrique',$numerote=true){
	include_spip("base/abstract_sql");
	$table = table_objet($type);
	$table_sql = table_objet_sql($type);
	$key = id_table_objet($type);
	$parent = ($type=='rubrique')?'id_parent':'id_rubrique';

	$cond = "";
	$zero = true;
	if ($numerote && ($type=='article')){
		$row = false;
		if (defined('_NUMERO_MOT_ARTICLE_ACCUEIL')) {
			// numeroter 0. l'article d'accueil de la rubrique
			$row = sql_fetsel("a.id_article,a.titre",
				"spip_articles AS a INNER JOIN spip_mots_liens as J ON (J.id_objet=a.id_article AND J.objet='article')",
				"a.id_rubrique=".sql_quote($id_rubrique)."
			 AND J.id_mot=".sql_quote(_NUMERO_MOT_ARTICLE_ACCUEIL),'',"0+a.titre, a.maj DESC","0,1");
		}
		if (defined('_DIR_PLUGIN_FONDS')){
			// numeroter 0. l'article d'accueil de la rubrique
			$row = sql_fetsel("a.id_article,a.titre",
				"spip_articles AS a INNER JOIN spip_rubriques as J ON J.id_accueil=a.id_article",
				"a.id_rubrique=".sql_quote($id_rubrique),'',"0+a.titre, a.maj DESC","0,1");
		}
		if ($row){
			$titre = "0. " . numero_denumerote_titre($row['titre']);
			sql_updateq($table_sql,array('titre'=>$titre),"$key=".sql_quote($row[$key]));
			$zero = false;
			$cond = " AND id_article<>".sql_quote($row[$key]);
		}
	}
	if ($type=='article') {
		$cond .= " AND statut!='poubelle'";
	}
	
	$res = sql_select("$key,titre",$table_sql,"$parent=".sql_quote($id_rubrique)."$cond ORDER BY 0+titre, maj DESC");
	$cpt = 1;
	while($row = spip_fetch_array($res)) {
		// conserver la numerotation depuis zero si deja presente
		if ($zero && ($cpt==1) && preg_match(',^0+[.]\s,',$row['titre'])) {
			$zero = false;
			$cpt = 0;
		}
		$titre = ($numerote?($cpt*10) . ". ":"") . numero_denumerote_titre($row['titre']);
		sql_updateq($table_sql,array('titre'=>$titre),"$key=".sql_quote($row[$key]));
		$cpt++;
	}
	return;
}

?>