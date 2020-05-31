<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Critere {tout_voir} permet de deverouiller l'acces restreint sur une boucle
 *
 * @param unknown_type $idb
 * @param unknown_type $boucles
 * @param unknown_type $crit
 */
if (!function_exists('critere_tout_voir_dist')) {
function critere_tout_voir_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->modificateur['tout_voir'] = true;
}
}

/*
 * Filtrage des objets suivant leurs éventuelles restrictions
 *
 * @pipeline pre_boucle
 */
function accesrestreint_pre_boucle($boucle) {
	// On ne filtre que s'il n'y a pas le critère {tout_voir} et pas la constante qui fait tout voir partout
	if ((!defined('AR_TOUJOURS_TOUT_VOIR') or !AR_TOUJOURS_TOUT_VOIR)
		and !isset($boucle->modificateur['tout_voir'])
		and $boucle->iterateur == 'SQL'
	) {
		$securise = false;

		switch ($boucle->type_requete) {
			case 'hierarchie':
				$primary = $boucle->id_table.'.'.$boucle->primary;
				$t = $boucle->id_table . '.id_rubrique';
				$boucle->select = array_merge($boucle->select, array($t, $primary)); // pour postgres
				// Test sur l'objet lui-même
				$boucle->where[] = accesrestreint_objets_accessibles_where($boucle->id_table, $primary);
				$securise = true;
				break;
			case 'articles':
			case 'breves':
			case 'syndication':
				$primary = $boucle->id_table.'.'.$boucle->primary;
				$t = $boucle->id_table . '.id_rubrique';
				$boucle->select = array_merge($boucle->select, array($t, $primary)); // pour postgres
				// Test sur l'objet lui-même
				$boucle->where[] = accesrestreint_objets_accessibles_where($boucle->id_table, $primary);
				// Test sur la hiérarchie
				$boucle->where[] = accesrestreint_rubriques_accessibles_where($t);
				$securise = true;
				break;
			case 'evenements':
			case 'petitions':
				$primary = $boucle->id_table.'.'.$boucle->primary;
				$t = $boucle->id_table . '.id_article';
				$boucle->select = array_merge($boucle->select, array($t, $primary));
				// Test sur l'objet lui-même
				$boucle->where[] = accesrestreint_objets_accessibles_where($boucle->id_table, $primary);
				// Test sur la hiérarchie
				$boucle->where[] = accesrestreint_articles_accessibles_where($t);
				$securise = true;
				break;
			case 'signatures':
				$primary = $boucle->id_table.'.'.$boucle->primary;
				// ajouter une jointure sur petitions si besoin
				$t = array_search('spip_petitions', $boucle->from);
				if (!$t) {
					$t = trouver_jointure_champ('id_petition', $boucle);
				}
				$t = $t . '.id_article';
				$boucle->select = array_merge($boucle->select, array($t));
				// Test sur l'objet lui-même
				$boucle->where[] = accesrestreint_objets_accessibles_where($boucle->id_table, $primary);
				// Test sur la hiérarchie
				$boucle->where[] = accesrestreint_articles_accessibles_where($t);
				$securise = true;
				break;
			default:
				if (function_exists($accessible_where = 'accesrestreint_'.$boucle->type_requete.'_accessibles_where')) {
					$t = $boucle->id_table . '.' . $boucle->primary;
					$boucle->select = array_merge($boucle->select, array($t));
					$boucle->where[] = $accessible_where($t);
					$securise = true;
				}
				break;
		}

		if ($securise) {
			$boucle->hash .= "if (!defined('_DIR_PLUGIN_ACCESRESTREINT')) {
			\$link_empty = generer_url_ecrire('admin_vider'); \$link_plugin = generer_url_ecrire('admin_plugin');
			\$message_fr = 'La restriction d\'acc&egrave;s a ete desactiv&eacute;e. <a href=\"'.\$link_plugin.'\">Corriger le probl&egrave;me</a> ou <a href=\"'.\$link_empty.'\">vider le cache</a> pour supprimer les restrictions.';
			\$message_en = 'Acces Restriction is now unusable. <a href=\"'.\$link_plugin.'\">Correct this trouble</a> or <a href=\"'.generer_url_ecrire('admin_vider').'\">empty the cache</a> to finish restriction removal.';
			die(\$message_fr.'<br />'.\$message_en);
			}";
		}
	}

	return $boucle;
}

/**
 * Renvoyer le code de la condition where pour filtrer avec la liste des objets accessibles d'un type précis
 *
 * Attention, cette fonction ne gère pas les héritages,
 * la hiérarchie, mais seulement les objets contenus directement dans des zones.
 *
 * @param string $objets
 * @param string $primary
 * @return string
 */
function accesrestreint_objets_accessibles_where($objets, $primary, $not = 'NOT', $_publique = '') {
	if (!$_publique) {
		$_publique = '!test_espace_prive()';
	}

	return "sql_in('$primary', accesrestreint_liste_objets_exclus('$objets', $_publique), '$not')";
}

/**
 * Renvoyer le code de la condition where pour la liste des rubriques accessibles
 *
 * @param string $primary
 * @return string
 */
function accesrestreint_rubriques_accessibles_where($primary, $not = 'NOT', $_publique = '') {
	if (!$_publique) {
		$_publique = '!test_espace_prive()';
	}
	$where = "sql_in('$primary', accesrestreint_liste_rubriques_exclues($_publique), '$not')";

	// Permettre aux plugins de modifier la condition
	$where = pipeline('accesrestreint_objets_accessibles_where',
		array(
			'args' => array(
				'table_objet' => 'rubriques',
				'primary'   => $primary,
				'publique' => $_publique,
				'not'       => $not,
			),
			'data' => $where
		)
	);

	return $where;
}

/**
 * Renvoyer la condition where pour la liste des articles accessibles
 *
 * @param string $primary
 * @return string
 */
function accesrestreint_articles_accessibles_where($primary, $_publique = '') {
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	$where =  "array('NOT IN','$primary','(SELECT * FROM('.sql_get_select('zzza.id_article','spip_articles as zzza',".accesrestreint_rubriques_accessibles_where('zzza.id_rubrique', '', $_publique).", '', '', '', '',\$connect).') AS subquery)')";
	$where = "array('AND', $where, ".accesrestreint_objets_accessibles_where('articles', $primary, 'not', $_publique).")";

	// Permettre aux plugins de modifier la condition
	$where = pipeline('accesrestreint_objets_accessibles_where',
		array(
			'args' => array(
				'table_objet' => 'articles',
				'primary'     => $primary,
				'publique'   => $_publique,
				'not'         => null,
			),
			'data' => $where
		)
	);

	return $where;
	#return array('SUBSELECT','id_article','spip_articles',array(".accesrestreint_rubriques_accessibles_where('id_rubrique').")))";
}

/**
 * Renvoyer la condition where pour la liste des breves accessibles
 *
 * @param string $primary
 * @return string
 */
function accesrestreint_breves_accessibles_where($primary, $_publique = '') {
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	$where = "array('NOT IN','$primary','(SELECT * FROM('.sql_get_select('zzzb.id_breve','spip_breves as zzzb',".accesrestreint_rubriques_accessibles_where('zzzb.id_rubrique', '', $_publique).", '', '', '', '',\$connect).') AS subquery)')";
	$where = "array('AND', $where, ".accesrestreint_objets_accessibles_where('breves', $primary, 'not', $_publique).")";

	// Permettre aux plugins de modifier la condition
	$where = pipeline('accesrestreint_objets_accessibles_where',
		array(
			'args' => array(
				'table_objet' => 'breves',
				'primary'     => $primary,
				'publique'   => $_publique,
				'not'         => null,
			),
			'data' => $where
		)
	);

	return $where;
	#return "array('IN','$primary',array('SUBSELECT','id_breve','spip_breves',array(".accesrestreint_rubriques_accessibles_where('id_rubrique').")))";
}

/**
 * Renvoyer le code de la condition where pour la liste des syndic articles accessibles
 *
 * @param string $primary
 * @return string
 */
function accesrestreint_syndic_articles_accessibles_where($primary, $_publique = '') {
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	$where = "array('NOT IN','$primary','(SELECT * FROM('.sql_get_select('zzzs.id_syndic','spip_syndic as zzzs',".accesrestreint_rubriques_accessibles_where('zzzs.id_rubrique', '', $_publique).", '', '', '', '',\$connect).') AS subquery)')";
	$where = "array('AND', $where, ".accesrestreint_objets_accessibles_where('syndic', $primary, 'not', $_publique).")";

	// Permettre aux plugins de modifier la condition
	$where = pipeline('accesrestreint_objets_accessibles_where',
		array(
			'args' => array(
				'table_objet' => 'syndic_articles',
				'primary'     => $primary,
				'publique'   => $_publique,
				'not'         => null,
			),
			'data' => $where
		)
	);

	return $where;
	#return "array('IN','$primary',array('SUBSELECT','id_syndic','spip_syndic',array(".accesrestreint_rubriques_accessibles_where('id_rubrique').")))";
}


/**
 * Renvoyer le code de la condition where pour la liste des forums accessibles
 * on ne rend visible que les forums qui sont lies a un article, une breve ou une rubrique visible
 *
 * @param string $primary
 * @return string
 */
function accesrestreint_forums_accessibles_where($primary, $_publique = '') {
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	$where = "array('AND','zzzf.objet=\'rubrique\'',".accesrestreint_rubriques_accessibles_where('zzzf.id_objet', 'NOT', $_publique).")";
	$where = "array('OR',$where,"
		."array('AND','zzzf.objet=\'article\'',".accesrestreint_articles_accessibles_where('zzzf.id_objet', $_publique).")"
		.")";
	$where = "array('OR',$where,"
		."array('AND','zzzf.objet=\'breve\'',".accesrestreint_breves_accessibles_where('zzzf.id_objet', $_publique).")"
		.")";
	if(!defined('AR_RESTRICTION_LIENS_STRICTE')) {
		$where = "array('OR',$where,sql_in('zzzf.objet',array('rubrique','article','breve'),'NOT',\$connect))";
	}
	
	// Permettre aux plugins de modifier la condition
	$where = pipeline('accesrestreint_objets_accessibles_where',
		array(
			'args' => array(
				'table_objet' => 'forums',
				'primary'     => $primary,
				'publique'   => $_publique,
				'not'         => null,
			),
			'data' => $where
		)
	);

	return "array('IN','$primary','(SELECT * FROM('.sql_get_select('zzzf.id_forum','spip_forum as zzzf',array($where),'','','','',\$connect).') AS subquery)')";
}


/**
 * Renvoyer le code de la condition where pour la liste des documents accessibles
 * on ne rend visible que les docs qui sont lies a un article, une breve ou une rubrique visible
 *
 * @param string $primary
 * @param mixed $_publique
 * @return string
 */
function accesrestreint_documents_accessibles_where($primary, $_publique = '') {
	# hack : on utilise zzz pour eviter que l'optimiseur ne confonde avec un morceau de la requete principale
	$where = "array('AND','zzzd.objet=\'rubrique\'',".accesrestreint_rubriques_accessibles_where('zzzd.id_objet', 'NOT', $_publique).")";
	$where = "array('OR',$where,array('AND','zzzd.objet=\'article\'',".accesrestreint_articles_accessibles_where('zzzd.id_objet', $_publique)."))";
	$where = "array('OR',$where,array('AND','zzzd.objet=\'breve\'',".accesrestreint_breves_accessibles_where('zzzd.id_objet', $_publique)."))";
	$where = "array('OR',$where,array('AND','zzzd.objet=\'forum\'',".accesrestreint_forums_accessibles_where('zzzd.id_objet', $_publique)."))";
	if(!defined('AR_RESTRICTION_LIENS_STRICTE')) {
		$where = "array('OR',$where,sql_in('zzzd.objet',array('rubrique','article','breve','forum'),'NOT',\$connect))";
	}

	$where = "array('OR',
	array('IN','$primary','(SELECT * FROM('.sql_get_select('zzzd.id_document','spip_documents_liens as zzzd',array($where),'','','','',\$connect).') AS subquery)'),
	array('NOT IN','$primary','(SELECT * FROM('.sql_get_select('zzzd.id_document','spip_documents_liens as zzzd','','','','','',\$connect).') AS subquery)')
	)";

	// Permettre aux plugins de modifier la condition
	$where = pipeline('accesrestreint_objets_accessibles_where',
		array(
			'args' => array(
				'table_objet' => 'documents',
				'primary'     => $primary,
				'publique'   => $_publique,
				'not'         => null,
			),
			'data' => $where
		)
	);

	return $where;
}


/*	Champs declares pour la recherche */
function accesrestreint_rechercher_liste_des_champs($tables) {
	$tables['zone']['titre'] = 8;
	$tables['zone']['descriptif'] = 3;

	return $tables;
}
