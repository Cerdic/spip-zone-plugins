<?php

function numero_denumerote_titre($titre){
	return preg_replace(',^([0-9]+[.]\s+),','',$titre);
}

function numero_numeroter_rubrique($id_rubrique,$type='rubrique',$numerote=true){
	$table = table_objet($type);
	$key = id_table_objet($type);
	$parent = ($type=='rubrique')?'id_parent':'id_rubrique';

	$cond = "";
	$zero = true;
	if ($numerote && ($type=='article')){
		$row = false;
		if (defined('_NUMERO_MOT_ARTICLE_ACCUEIL')) {
			// numeroter 0. l'article d'accueil de la rubrique
			$res = spip_query("SELECT a.id_article,a.titre FROM spip_articles AS a INNER JOIN spip_mots_articles as J ON J.id_article=a.id_article
			 WHERE a.id_rubrique="._q($id_rubrique)." 
			 AND J.id_mot="._q(_NUMERO_MOT_ARTICLE_ACCUEIL)."
			 ORDER BY 0+a.titre, a.maj DESC LIMIT 0,1");
			$row = spip_fetch_array($res);
		}
		if (defined('_DIR_PLUGIN_FONDS')){
			// numeroter 0. l'article d'accueil de la rubrique
			$res = spip_query($q="SELECT a.id_article,a.titre FROM spip_articles AS a INNER JOIN spip_rubriques as J ON J.id_accueil=a.id_article
			 WHERE a.id_rubrique="._q($id_rubrique)." 
			 ORDER BY 0+a.titre, a.maj DESC LIMIT 0,1");
			$row = spip_fetch_array($res);
		}	
		if ($row){
			$titre = "0. " . numero_denumerote_titre($row['titre']);
			spip_query("UPDATE spip_$table SET titre="._q($titre)." WHERE $key=".$row[$key]);
			$zero = false;
			$cond = " AND id_article<>"._q($row[$key]);
		}
	}
	if ($type=='article') {
		$cond .= " AND statut!='poubelle'";
	}
	
	$res = spip_query("SELECT $key,titre FROM spip_$table WHERE $parent="._q($id_rubrique)."$cond ORDER BY 0+titre, maj DESC");
	$cpt = 1;
	while($row = spip_fetch_array($res)) {
		// conserver la numerotation depuis zero si deja presente
		if ($zero && ($cpt==1) && preg_match(',^0+[.]\s,',$row['titre'])) {
			$zero = false;
			$cpt = 0;
		}
		$titre = ($numerote?($cpt*10) . ". ":"") . numero_denumerote_titre($row['titre']);
		spip_query("UPDATE spip_$table SET titre="._q($titre)." WHERE $key=".$row[$key]);
		$cpt++;
	}
	return;
}

?>