<?php
function masquer_pre_boucle($boucle) {
  static $id_mot;
  static $liste_rubriques;
  static $liste_articles;

  // On ne masque que sur l'espace public
  if (test_espace_prive() || isset($boucle->modificateur['tout_voir'])) {
    return $boucle;
  }

  // Cas de la boucle RUBRIQUES
  if ($boucle->type_requete == 'rubriques') {
    $rub = $boucle->id_table . '.id_rubrique';
    $boucle->where[] = masquer_rubriques_where($rub);
  }

  // Cas de la boucle ARTICLES
  if ($boucle->type_requete == 'articles') {
    $art = $boucle->id_table . '.id_article';
    $boucle->where[] = masquer_articles_accessibles_where($art);
  }

  return $boucle;
}

/**
 * Renvoyer le code de la condition where pour la liste des rubriques masquées
 *
 * @param string $primary
 * @return string
 */
function masquer_rubriques_where($primary, $_publique=''){
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	return "array('NOT IN','$primary','('.sql_get_select('zzzr.id_rubrique','spip_mots_rubriques as zzzr, spip_mots as zzzm',' zzzr.id_mot=zzzm.id_mot AND zzzm.titre=\'masquer\'','','','','',\$connect).')')";
}

/**
 * Renvoyer le code de la condition where pour la liste des rubriques accessibles
 *
 * @param string $primary
 * @return string
 */
function masquer_rubriques_accessibles_where($primary,$not='NOT', $_publique=''){
	return "sql_in('$primary','".implode(',', masquer_liste_rubriques($_publique))."', '$not')";
}

/**
 * liste des rubriques masquer, directement ou par heritage.
 *
 * @param int/string $id_zone
 * @return array
 */
function masquer_liste_rubriques($publique=true){
	// cache static
	static $liste_rubriques = array();
	include_spip('inc/rubriques');
	$liste_rubriques = masquer_liste_rub_direct();
	if (!count($liste_rubriques))
		return $liste_rubriques;
	$liste_rubriques = calcul_branche_in(join(',',$liste_rubriques));
	if (!strlen($liste_rubriques))
		return array();
	$liste_rubriques = explode(',',$liste_rubriques);
	return $liste_rubriques;
}

/**
 * liste des rubriques masquer directement.
 *
 * @return array
 */
function masquer_liste_rub_direct(){
	$liste_rubriques=array();
	// liste des rubriques directement masquer
	$where = array();
	include_spip('base/abstract_sql');
	$liste_rubriques = sql_allfetsel('id_rubrique','spip_mots_rubriques AS mr INNER JOIN spip_mots AS m ON mr.id_mot=m.id_mot','m.titre=\'masquer\'');
	$liste_rubriques = array_map('reset',$liste_rubriques);
	$liste_rubriques = array_unique($liste_rubriques);
	return $liste_rubriques;
}

/**
 * Renvoyer la condition where pour la liste des articles masquer
 *
 * @param string $primary
 * @return string
 */
function masquer_articles_accessibles_where($primary, $_publique=''){
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	return "array('NOT IN','$primary','('.sql_get_select('zzza.id_article','spip_articles as zzza',".masquer_rubriques_accessibles_where('zzza.id_rubrique','',$_publique).",'','','','',\$connect).')')";
}

?>