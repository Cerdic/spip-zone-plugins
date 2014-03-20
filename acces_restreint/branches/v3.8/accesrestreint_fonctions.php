<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('public/accesrestreint');
include_spip('inc/accesrestreint');

/**
 * filtre de securisation des squelettes
 * utilise avec [(#REM|accesrestreint_securise_squelette)]
 * evite divulgation d'info si plugin desactive
 * par erreur fatale
 *
 * @param unknown_type $letexte
 * @return unknown
 */
function accesrestreint_securise_squelette($letexte){
	return "";
}


/**
 * filtre de test pour savoir si l'acces a un article est restreint
 *
 * @param int $id_article
 * @param int $id_auteur
 * @return bool
 */
function accesrestreint_article_restreint($id_article, $id_auteur=null){
	include_spip('public/quete');
	include_spip('inc/accesrestreint');
	$article = quete_parent_lang('spip_articles',$id_article);
	return
		@in_array($article['id_rubrique'],
			accesrestreint_liste_rubriques_exclues(!test_espace_prive(), $id_auteur)
		);
}


/**
 * filtre de test pour savoir si l'acces a une rubrique est restreinte
 *
 * @param int $id_rubrique
 * @param int $id_auteur
 * @return bool
 */
function accesrestreint_rubrique_restreinte($id_rubrique, $id_auteur=null){
	include_spip('inc/accesrestreint');
	return
		@in_array($id_rubrique,
			accesrestreint_liste_rubriques_exclues(!test_espace_prive(), $id_auteur)
		);
}

/**
 * Filtre pour tester l'appartenance d'un auteur a une zone
 *
 * @param int $id_zone
 * @param int $id_auteur
 * @return bool
 */
function accesrestreint_acces_zone($id_zone,$id_auteur=null){
	static $liste_zones = array();
	if (is_null($id_auteur)) $id_auteur=$GLOBALS['visiteur_session']['id_auteur'];
	if (!isset($liste_zones[$id_auteur])){
		if ($GLOBALS['accesrestreint_zones_autorisees']
		  AND ($id_auteur==$GLOBALS['visiteur_session']['id_auteur']))
			$liste_zones[$id_auteur] = explode(',',$GLOBALS['accesrestreint_zones_autorisees']);
		elseif (!is_null($id_auteur)){
			include_spip('inc/accesrestreint');
			$liste_zones[$id_auteur] = explode(',',accesrestreint_liste_zones_autorisees('',$id_auteur));
		}
	}
	
	return in_array($id_zone,$liste_zones[$id_auteur]);
}

/**
 * fonction pour afficher une icone 12px selon le statut de l'auteur
 *
 * @param string $statut
 * @return string
 */
function icone_auteur_12($statut){
	if ($statut=='0minirezo') return _DIR_IMG_PACK . 'admin-12.gif';
	if ($statut=='1comite') return _DIR_IMG_PACK . 'redac-12.gif';
	return _DIR_IMG_PACK . 'visit-12.gif';
}





/**
 * Retroune les identifiants des zones a laquelle appartient une rubrique
 * et ses rubriques parentes
 * (quelquesoit la visibilité de la zone (publique, prive, les 2 ou aucun)
 *
 * @param int $id_rubrique
 * @return array identifiants des zones
 */
function accesrestreint_zones_rubrique_et_hierarchie($id_rubrique) {
	static $zones = array();
	
	if (!$id_rubrique) {
		return array();
	}
	
	if (isset($zones[$id_rubrique])) {
		return $zones[$id_rubrique];
	}

	// on teste notre rubrique deja
	$idz = accesrestreint_zones_rubrique($id_rubrique);
	
	// on parcours toute l'arborescence jusqu'a la racine en testant les zones
	// pour completer les zones deja trouvees
	if ($id_parent = sql_getfetsel('id_parent', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique))) {
		// on teste notre parent
		$idz_parent = accesrestreint_zones_rubrique_et_hierarchie($id_parent);
		$idz = array_merge($idz, $idz_parent);
	}

	// on renvoie la rubrique
	return $zones[$id_rubrique] = $idz;
}


/**
 * Retroune les identifiants des zones a laquelle appartient une rubrique
 * (quelquesoit la visibilité de la zone (publique, prive, les 2 ou aucun)
 * 
 * @param int $id_rubrique
 * @return array identifiants des zones
 */
function accesrestreint_zones_rubrique($id_rubrique) {
	// on teste notre rubrique deja
	$idz = sql_allfetsel('id_zone', 'spip_zones_liens', "objet='rubrique' AND id_objet=". intval($id_rubrique));
	if (is_array($idz)) {
		$idz = array_map('reset', $idz);
		return $idz;
	}
	return array();
}

/**
 * Cherche si la rubrique donnée est inclue dans une zone d'accès restreinte.
 * 
 * [(#ID_RUBRIQUE|accesrestreint_rubrique_zone_restreinte|oui) Rubrique non visible dans une zone]
 * [(#ID_RUBRIQUE|accesrestreint_rubrique_zone_restreinte{tout}) Rubrique dans une zone ]
 *
 * @param int $id_rubrique : identifiant de la rubrique
 * @param null|bool|string $_publique
 *   Sélectionner les rubriques
 *   cachées dans le public (true),
 *   le privé (false),
 *   selon le contexte privé ou public (null),
 *   cachées ou non quelque soit le contexte ('tout')
 * @return bool La rubrique est présente dans une zone
 */
function accesrestreint_rubrique_zone_restreinte($id_rubrique, $_publique=null) {
	return
		@in_array($id_rubrique,
			accesrestreint_liste_rubriques_restreintes_et_enfants($_publique)
		);
}

/**
 * Retourne la liste de toutes les rubriques sélectionnées dans des zones 
 *
 * @param null|bool|string	$_publique
 *   Sélectionner les rubriques
 *   cachées dans le public (true),
 *   le privé (false),
 *   selon le contexte privé ou public (null),
 *   cachées ou non quelque soit le contexte ('tout')
 * @return Array liste d'identifiants de rubriques
 */
function accesrestreint_liste_rubriques_restreintes($_publique = null) {
	static $rubs = array();

	// $_publique : null, true, false, 'tout'
	$tout = false;
	if (is_null($_publique)) {
		$_publique = !test_espace_prive();
	} elseif ($_publique === 'tout') {
		$tout = true;
	}

	if (isset($rubs[$_publique])) {
		return $rubs[$_publique];
	}

	$where = array("zr.objet='rubrique'");
	if (!$tout) {
		if ($_publique) {
			$where[] = 'z.publique=' . sql_quote('oui');
		} else {
			$where[] = 'z.privee=' . sql_quote('oui');
		}
	}
	
	$idz = sql_allfetsel('DISTINCT(zr.id_objet)', 'spip_zones_liens AS zr JOIN spip_zones AS z ON z.id_zone = zr.id_zone', $where);
	
	if (is_array($idz)) {
		$idz = array_map('reset', $idz);
		return $rubs[$_publique] = $idz;
	}
	
	return $rubs[$_publique] = array();
}


/**
 * Retourne la liste de toutes les rubriques sélectionnées dans des zones 
 *
 * @param null|bool|string $_publique
 *   Sélectionner les rubriques
 *   cachées dans le public (true),
 *   le privé (false),
 *   selon le contexte privé ou public (null),
 *   cachées ou non quelque soit le contexte ('tout')
 * @return Array liste d'identifiants de rubriques
 */
function accesrestreint_liste_rubriques_restreintes_et_enfants($_publique = null) {
	static $rubs = array();

	if (is_null($_publique)) {
		$_publique = !test_espace_prive();
	}

	if (isset($rubs[$_publique])) {
		return $rubs[$_publique];
	}
	
	$parents = accesrestreint_liste_rubriques_restreintes($_publique);

	if ($parents) {
		include_spip('inc/rubriques');
		$branches = explode(',', calcul_branche_in($parents));
		return $rubs[$_publique] = $branches;
	}
	
	return $rubs[$_publique] = array();
}

/**
 * Un auteur donné fait il partie d'une zone permettant de voir telle rubrique.
 * Renvoie true, si l'auteur peut voir la rubrique,
 * quelquesoit la visibilité des rubriques de la zone 
 *
 * @param int $id_auteur	Identifiant de l'auteur
 * @param int $id_rubrique	Identifiant de la rubrique
 * @return bool L'auteur fait partie de la rubrique.
 */
function accesrestreint_auteur_lie_a_rubrique($id_auteur, $id_rubrique) {
	if (!$id_auteur)   return false;
	if (!$id_rubrique) return false;
	// $auteur[3][8] : l'auteur 3 ne peut pas voir la rubrique 8
	static $auteurs = array();
	if (!isset($auteurs[$id_auteur])) {
		$auteurs[$id_auteur] = array();

		include_spip('inc/accesrestreint');
		$auteurs[$id_auteur] = array_flip(accesrestreint_liste_rubriques_exclues(true, $id_auteur, true));
	}
	
	// si la rubrique est presente, c'est qu'on ne peut pas la voir !
	if (isset($auteurs[$id_auteur][$id_rubrique])) {
		return false;
	}
	
	return true;
	
}


?>
