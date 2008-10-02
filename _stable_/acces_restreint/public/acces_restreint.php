<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Critere {tout_voir} permet de deverouiller l'acces restreint sur une boucle
 *
 * @param unknown_type $idb
 * @param unknown_type $boucles
 * @param unknown_type $crit
 */
function critere_tout_voir_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->modificateur['tout_voir'] = true;
}

function AccesRestreint_pre_boucle(&$boucle){
	if (!isset($boucle->modificateur['tout_voir'])){
		switch ($boucle->type_requete){
			case 'hierarchie':
			case 'rubriques':
			case 'articles':
			case 'breves':
			case 'syndication':
				$t = $boucle->id_table . '.id_rubrique';
				$boucle->select = array_merge($boucle->select, array($t)); // pour postgres
				$boucle->where[] = AccesRestreint_rubriques_accessibles_where($t);
				break;
			case 'forums':
				$t = $boucle->id_table . '.id_rubrique';
				$boucle->select = array_merge($boucle->select, array($t)); // pour postgres
				$where = AccesRestreint_rubriques_accessibles_where($t);
		
				$t = $boucle->id_table . '.id_article';
				$boucle->select = array_merge($boucle->select, array($t)); // pour postgres
				$where = "array('OR',$where,".AccesRestreint_articles_accessibles_where($t).")";
		
				$t = $boucle->id_table . '.id_breve';
				$boucle->select = array_merge($boucle->select, array($t)); // pour postgres
				$boucle->where[] = "array('OR',$where,".AccesRestreint_breves_accessibles_where($t).")";
				break;
			case 'evenements':
			case 'signatures':
				$t = $boucle->id_table . '.id_article';
				$boucle->select = array_merge($boucle->select, array($t));
				$boucle->where[] = AccesRestreint_articles_accessibles_where($t);
				break;
			case 'syndic_articles':
				$t = $boucle->id_table . '.' . $boucle->primary;
				$boucle->select = array_merge($boucle->select, array($t));
				$boucle->where[] = AccesRestreint_syndic_articles_accessibles_where($t);
				break;
			case 'documents':
				$t = $boucle->id_table . '.' . $boucle->primary;
				$boucle->select = array_merge($boucle->select, array($t));
				$boucle->where[] = AccesRestreint_documents_accessibles_where($t);
				break;
		}
	}
	return $boucle;
}


/**
 * Renvoyer le code de la condition where pour la liste des rubriques accessibles
 *
 * @param string $primary
 * @return string
 */
function AccesRestreint_rubriques_accessibles_where($primary,$not='NOT', $_publique=''){
	if (!$_publique) $_publique = "!test_espace_prive()";
	return "sql_in('$primary', AccesRestreint_liste_rubriques_exclues($_publique), '$not')";
}

/**
 * Renvoyer la condition where pour la liste des articles accessibles
 *
 * @param string $primary
 * @return string
 */
function AccesRestreint_articles_accessibles_where($primary, $_publique=''){
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	return "array('NOT IN','$primary','('.sql_get_select('zzza.id_article','spip_articles as zzza',".AccesRestreint_rubriques_accessibles_where('zzza.id_rubrique','',$_publique).",'','','','',\$connect).')')";
	#return array('SUBSELECT','id_article','spip_articles',array(".AccesRestreint_rubriques_accessibles_where('id_rubrique').")))";
}

/**
 * Renvoyer la condition where pour la liste des breves accessibles
 *
 * @param string $primary
 * @return string
 */
function AccesRestreint_breves_accessibles_where($primary, $_publique=''){
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	return "array('NOT IN','$primary','('.sql_get_select('zzzb.id_breve','spip_breves as zzzb',".AccesRestreint_rubriques_accessibles_where('zzzb.id_rubrique','',$_publique).",'','','','',\$connect).')')";
	#return "array('IN','$primary',array('SUBSELECT','id_breve','spip_breves',array(".AccesRestreint_rubriques_accessibles_where('id_rubrique').")))";
}

/**
 * Renvoyer le code de la condition where pour la liste des syndic articles accessibles
 *
 * @param string $primary
 * @return string
 */
function AccesRestreint_syndic_articles_accessibles_where($primary, $_publique=''){
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	return "array('NOT IN','$primary','('.sql_get_select('zzzs.id_syndic','spip_syndic as zzzs',".AccesRestreint_rubriques_accessibles_where('zzzs.id_rubrique','',$_publique).",'','','','',\$connect).')')";
	#return "array('IN','$primary',array('SUBSELECT','id_syndic','spip_syndic',array(".AccesRestreint_rubriques_accessibles_where('id_rubrique').")))";
}

/**
 * Renvoyer le code de la condition where pour la liste des documents accessibles
 * on ne rend visible que les docs qui sont lies a un article, une breve ou une rubrique visible
 *
 * @param string $primary
 * @return string
 */
function AccesRestreint_documents_accessibles_where($primary, $_publique=''){
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	return "array('IN','$primary','('.sql_get_select('zzz.id_document','spip_documents_liens as zzz',
	array(array('OR',
		array('OR',
			array('AND','zzz.objet=\'rubrique\'',".AccesRestreint_rubriques_accessibles_where('zzz.id_objet','NOT',$_publique)."),
			array('AND','zzz.objet=\'article\'',".AccesRestreint_articles_accessibles_where('zzz.id_objet',$_publique).")
		),
			array('AND','zzz.objet=\'breve\'',".AccesRestreint_breves_accessibles_where('zzz.id_objet',$_publique).")
	))"
	.",'','','','',\$connect).')')";
	/*return "array('IN','$primary',array('SUBSELECT','id_document','spip_documents_liens',
	array(array('OR',
		array('OR',
			array('AND','objet=\'rubrique\'',".AccesRestreint_rubriques_accessibles_where('id_objet')."),
			array('AND','objet=\'article\'',".AccesRestreint_articles_accessibles_where('id_objet').")
		),
			array('AND','objet=\'breve\'',".AccesRestreint_breves_accessibles_where('id_objet').")
	))
	))";*/
}

?>