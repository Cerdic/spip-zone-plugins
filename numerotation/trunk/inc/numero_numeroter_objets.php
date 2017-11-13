<?php
/*
 * Plugin numero
 * aide a la numerotation/classement des objets dans l'espace prive
 *
 * Auteurs :
 * Cedric Morin, Nursit.com
 * (c) 2008-2014 - Distribue sous licence GNU/GPL
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Numeroter/denumeroter les objets d'un parent
 * @param string $type
 * @param int $id_parent
 * @param bool $remove
 */
function inc_numero_numeroter_objets_dist($type='rubrique',$id_parent,$remove=false){
	include_spip('base/abstract_sql');
	include_spip('inc/numeroter');

	$d = numero_info_objet($type);
	if (!$d)
		return;

	$type = $d['type'];
	$table = $d['table'];
	$table_sql = $d['table_sql'];
	$key = $d['primary'];
	$desc = $d['desc'];
	$parent = $d['parent'];
	$titre = $d['titre'];

	$cond = array();
	$zero = true;
	if (!$remove AND
		$type=='article'){
		$row = false;
		if (defined('_NUMERO_MOT_ARTICLE_ACCUEIL')) {
			// numeroter 0. l'article d'accueil de la rubrique
			$row = sql_fetsel("a.id_article,a.titre",
				"spip_articles AS a INNER JOIN spip_mots_liens as J ON (J.id_objet=a.id_article AND J.objet='article')",
				"a.id_rubrique=".sql_quote($id_parent)."
			 AND J.id_mot=".sql_quote(_NUMERO_MOT_ARTICLE_ACCUEIL),'',"0+a.titre, a.maj DESC","0,1");
		}
		if (defined('_DIR_PLUGIN_ARTICLE_ACCUEIL')){
			// numeroter 0. l'article d'accueil de la rubrique
			$row = sql_fetsel("a.id_article,a.titre",
				"spip_articles AS a INNER JOIN spip_rubriques as J ON J.id_article_accueil=a.id_article",
				"a.id_rubrique=".sql_quote($id_parent),'',"0+a.titre, a.maj DESC","0,1");
		}
		if ($row){
			$titre = "0. " . numero_denumerote_titre($row['titre']);
			if ($titre!==$row['titre'])
				sql_updateq($table_sql,array('titre'=>$titre),"$key=".sql_quote($row[$key]));
			$zero = false;
			$cond[] = "id_article<>".sql_quote($row[$key]);
		}
	}
	if ($type=='article') {
		$cond[] = "statut!=".sql_quote('poubelle');
	}

	if ($parent){
		$cond[] = "$parent=".sql_quote($id_parent);
	}

	$res = numero_requeter_titre($type,$cond);
	$cpt = 1;
	while($row = sql_fetch($res)) {
		// conserver la numerotation depuis zero si deja presente
		if ($zero && ($cpt==1) && preg_match(',^0+[.]\s,',$row['titre'])) {
			$zero = false;
			$cpt = 0;
		}
		$t = (!$remove?($cpt*_NUMEROTE_STEP) . ". ":"") . numero_denumerote_titre($row['titre']);
		if ($t!==$row['titre']){
			numero_titrer_objet($type,$row['id'],$t);
		}
		$cpt++;
	}
	return;
}
