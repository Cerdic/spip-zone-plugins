<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * filtre de securisation des squelettes
 * utilise avec [(#REM|AccesRestreint_securise_squelette)]
 * evite divulgation d'info si plugin desactive
 * par erreur fatale
 *
 * @param unknown_type $letexte
 * @return unknown
 */
function AccesRestreint_securise_squelette($letexte){
	return "";
}


/**
 * filtre de test pour savoir si l'acces a un article est restreint
 *
 * @param int $id_article
 * @return bool
 */
function AccesRestreint_article_restreint($id_article){
	include_spip('public/quete');
	$article = quete_parent_lang('spip_articles',$id_article);
	include_spip('inc/acces_restreint');
	return
		@in_array($article['id_rubrique'],
			AccesRestreint_liste_rubriques_exclues(!test_espace_prive())
		);
}


/**
 * filtre de test pour savoir si l'acces a une rubrique est restreinte
 *
 * @param int $id_rubrique
 * @return bool
 */
function AccesRestreint_rubrique_restreinte($id_rubrique){
	include_spip('inc/acces_restreint');
	return
		@in_array($id_rubrique,
			AccesRestreint_liste_rubriques_exclues(!test_espace_prive())
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
		if ($GLOBALS['AccesRestreint_zones_autorisees']
		  AND ($id_auteur==$GLOBALS['visiteur_session']['id_auteur']))
			$liste_zones[$id_auteur] = explode(',',$GLOBALS['AccesRestreint_zones_autorisees']);
		elseif (!is_null($id_auteur))
			$liste_zones[$id_auteur] = AccesRestreint_liste_zones_autorisees('',$id_auteur);
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

?>