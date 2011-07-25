<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

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
 * @return bool
 */
function accesrestreint_article_restreint($id_article, $id_auteur=null){
	include_spip('public/quete');
	include_spip('inc/acces_restreint');
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
 * @return bool
 */
function accesrestreint_rubrique_restreinte($id_rubrique, $id_auteur=null){
	include_spip('inc/acces_restreint');
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
 */
function accesrestreint_acces_zone($id_zone,$id_auteur=null){
	static $liste_zones = array();
	if (is_null($id_auteur)) $id_auteur=$GLOBALS['visiteur_session']['id_auteur'];
	if (!isset($liste_zones[$id_auteur])){
		if ($GLOBALS['accesrestreint_zones_autorisees']
		  AND ($id_auteur==$GLOBALS['visiteur_session']['id_auteur']))
			$liste_zones[$id_auteur] = explode(',',$GLOBALS['accesrestreint_zones_autorisees']);
		elseif (!is_null($id_auteur)){
			include_spip('inc/acces_restreint');
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
**/
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
**/
function accesrestreint_zones_rubrique($id_rubrique) {
	// on teste notre rubrique deja
	$idz = sql_allfetsel('id_zone', 'spip_zones_rubriques', 'id_rubrique='. intval($id_rubrique));
	if (is_array($idz)) {
		$idz = array_map('reset', $idz);
		return $idz;
	}
	return array();
}


/**
 * Retourne la liste de toutes les rubriques sélectionnées dans des zones 
 *
 * @return Array liste d'identifiants de rubriques
**/
function accesrestreint_liste_rubriques_restreintes() {
	static $rubs = array();

	$_publique = !test_espace_prive();

	if (isset($rubs[$_publique])) {
		return $rubs[$_publique];
	} 
	
	if ($_publique) {
		$p = 'publique=' . sql_quote('oui');
	} else {
		$p = 'prive=' . sql_quote('non');
	}
	
	$idz = sql_allfetsel('DISTINCT(id_rubrique)', array('spip_zones_rubriques AS zr', 'spip_zones AS z'), array(
		'z.id_zone = zr.id_zone', $p
	));
	
	if (is_array($idz)) {
		$idz = array_map('reset', $idz);
		return $rubs[$_publique] = $idz;
	}
	
	return $rubs[$_publique] = array();
}


/**
 * Retourne la liste de toutes les rubriques sélectionnées dans des zones 
 *
 * @return Array liste d'identifiants de rubriques
**/
function accesrestreint_liste_rubriques_restreintes_et_enfants() {
	static $rubs = array();

	$_publique = !test_espace_prive();

	if (isset($rubs[$_publique])) {
		return $rubs[$_publique];
	}

	$parents = accesrestreint_liste_rubriques_restreintes();
	if ($parents) {
		include_spip('inc/rubriques');
		$branches = explode(',', calcul_branche_in($parents));
		return $rubs[$_publique] = $branches;
	}
	
	return $rubs[$_publique] = array();
}
?>
