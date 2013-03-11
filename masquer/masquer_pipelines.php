<?php

if(!defined('_MOT_MASQUER')) define('_MOT_MASQUER', 'masquer');
if(!defined('_SPIP30000') && $GLOBALS['spip_version_code']>=17743) define('_SPIP30000', 1);

function masquer_pre_boucle($boucle) {
  // On ne masque pas si : espace prive ou critere {tout_voir}
  if (test_espace_prive() || isset($boucle->modificateur['tout_voir']))
    return $boucle;

  $type = $boucle->type_requete;
  if ($type == 'rubriques') {
 	// Cas de la boucle RUBRIQUES
    $rub = $boucle->id_table . '.id_rubrique';
    $boucle->where[] = masquer_rubriques_where($rub); # par mot-clef et par branche
	return $boucle;
  } elseif ($type == 'articles') {
	// Cas de la boucle ARTICLES
    $art = $boucle->id_table . '.id_article';
    $boucle->where[] = masquer_objets_where($art, 'article'); # par mot-clef
    $boucle->where[] = masquer_articles_accessibles_where($art); # par branche
//    $boucle->where[] = masquer_articles_where($art); # par mot-clef et par branche
  }

  return $boucle;
}

/**
 * liste des objets directement masques par mot-clef
 *
 * @return array
 */
function masquer_liste_objets_direct($objet){
	static $liste = array();
	if(isset($liste[$objet]))
		return $liste[$objet];
	// liste des objets directement masques
	include_spip('base/abstract_sql');
	$tmp = defined('_SPIP30000')
		?sql_allfetsel('id_objet',"spip_mots_liens AS ml INNER JOIN spip_mots AS m ON (ml.id_mot=m.id_mot AND ml.objet='$objet')", 'm.titre='.sql_quote(_MOT_MASQUER))
		:sql_allfetsel('id_'.$objet,"spip_mots_{$objet}s AS mo INNER JOIN spip_mots AS m ON mo.id_mot=m.id_mot", 'm.titre='.sql_quote(_MOT_MASQUER));
	// remontee d'un niveau
	$tmp = array_map('reset', $tmp);
	return $liste[$objet] = array_unique($tmp);
}

/**
 * liste des articles masquees, directement par mot-clef ou par branche.
 *
 * @param bool $publie
 * @return array
 */
function masquer_liste_articles($publie=false){
	// cache static
	static $liste_articles = array();
	if(isset($liste_articles[$publie])) 
		return $liste_articles[$publie];
	// liste des articles contenus dans des rubriques masquees
	include_spip('base/abstract_sql');
	$tmp = sql_allfetsel('id_article', 'spip_articles as ma', ($publie?"ma.statut='publie' AND ":'') . sql_in('ma.id_rubrique', masquer_liste_rubriques($publie)));
	if (!count($tmp))
		return $liste_articles[$publie] = masquer_liste_objets_direct('article');
	$tmp = array_map('reset', $tmp);
	$tmp = array_unique(array_merge($tmp, masquer_liste_objets_direct('article')));
	return $liste_articles[$publie] = $tmp;
}

/**
 * liste des rubriques masquees, directement par mot-clef ou par heritage.
 *
 * @param bool $publie
 * @return array
 */
function masquer_liste_rubriques($publie=false){
	// cache static
	static $liste_rubriques = array();
	if(isset($liste_rubriques[$publie])) 
		return $liste_rubriques[$publie];
	$tmp = masquer_liste_objets_direct('rubrique');
	if (!count($tmp))
		return $liste_rubriques[$publie] = array();
	include_spip('inc/rubriques');
	$tmp = calcul_branche_in(join(',', $tmp));
	if (!strlen($tmp))
		return $liste_rubriques[$publie] = array();
	if($publie) {
		$tmp = sql_allfetsel('id_rubrique', 'spip_rubriques as mr', "mr.statut='publie' AND " . sql_in('mr.id_rubrique', $tmp));
		return $liste_rubriques[$publie] = array_map('reset', $tmp);
	}
	return $liste_rubriques[$publie] = explode(',', $tmp);
}

/**
 * Renvoyer le code de la condition where pour la liste des objets masques par mot-clef
 *
 * @param string $primary
 * @return string
 */
function masquer_objets_where($primary, $objet, $not='NOT', $_publique=''){
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	if(defined('_SPIP30000'))
		return "sql_in('$primary',sql_get_select('zzzl.id_objet','spip_mots_liens as zzzl INNER JOIN spip_mots as zzzm ON (zzzl.id_mot=zzzm.id_mot AND zzzl.objet=\'$objet\')',\"zzzm.titre=".sql_quote(_MOT_MASQUER)."\",'','','','',\$connect), '$not')";
	return "sql_in('$primary',sql_get_select('zzzo.id_$objet','spip_mots_{$objet}s as zzzo, spip_mots as zzzm',\"zzzo.id_mot=zzzm.id_mot AND zzzm.titre=".sql_quote(_MOT_MASQUER)."\",'','','','',\$connect), '$not')";
}

/**
 * Renvoyer le code de la condition where pour la liste des rubriques masquees, directement par mot-clef ou par heritage.
 *
 * @param string $primary
 * @return string
 */
function masquer_rubriques_where($primary, $not='NOT', $_publique=''){
	return "sql_in('$primary','".implode(',', masquer_liste_rubriques())."', '$not')";
}

/**
 * Renvoyer la condition where pour la liste des articles dont la rubrique est masquee
 *
 * @param string $primary
 * @return string
 */
function masquer_articles_accessibles_where($primary, $not='NOT', $_publique=''){
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	return "sql_in('$primary',sql_get_select('zzza.id_article','spip_articles as zzza',".masquer_rubriques_where('zzza.id_rubrique','',$_publique).",'','','','',\$connect), '$not')";
}

/**
 * Renvoyer la condition where pour la liste de tous les articles masques, directement par mot-cle pour apartenant a une rubrique masquee
 *
 * @param string $primary
 * @return array
 */
function masquer_articles_where($primary, $_publique=''){
	return "array('AND', ".masquer_objets_where($primary, 'article').', '.masquer_articles_accessibles_where($primary).')';
}

?>