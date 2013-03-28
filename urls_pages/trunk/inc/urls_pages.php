<?php
/**
 * Fonctions
 *
 * @plugin     URLs pages personnalisées
 * @copyright  2013
 * @author     Charles Razack
 * @licence    GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Renvoie une liste des squelettes actifs de type "page"
 * par "page" on entend : squelette qui ne correspond à aucun objet éditorial.
 *
 * On va chercher les squelettes dans les dossiers suivants :
 *   - racine du ou des dossiers de squelettes (si Z: /content ou /contenu)
 *   - racine des dossiers des plugins actifs de catégorie "squelette" (si Z: /content ou /contenu)
 * Puis on filtres les squelettes qui ne sont pas des pages :
 *  - ceux correspondant à un objet éditorial (article*, rurbique*, etc.)
 *  - liste prédéfinie à exclure : sommaire, login, inc-*...
 *  - squelettes 'techniques' (z_apl, 404...)
 *
 * @return array
 *     liste des pages de la forme (page1=>dossier, page2=>dossier ...)
 */
function urls_pages_lister_pages () {

	// 1: lister les répertoires dans lesquels on peut chercher les squelettes des pages
	// répertoire(s) des squelettes
	$liste_dossiers_squelettes = ( strlen($dossiers = $GLOBALS['dossier_squelettes']) ) ? explode (':', $dossiers) : array("squelettes");
	foreach ( $liste_dossiers_squelettes as $dossier )
		$dossiers_squelettes[] = _DIR_RACINE.$dossier;
	// répertoires des plugins actifs dans la catégorie squelette
	if ($r = sql_select(
		array(
			'paquets.id_plugin',
			'plugins.id_plugin',
			'plugins.prefixe AS prefixe',
			'plugins.categorie AS categorie',
			'paquets.actif AS actif',
			'paquets.src_archive AS dossier',
			'paquets.constante'
		),
		array(
			'spip_paquets AS paquets',
			'spip_plugins AS plugins'
		),
		array (
			'paquets.id_plugin = plugins.id_plugin',
			'categorie = "squelette"',
			'actif = "oui"'
		))) {
		while ($ligne = sql_fetch($r)) {
			$prefixe = strtolower($ligne['prefixe']);
			// stocker la liste des dossiers des plugins
			// ne pas prendre en compte les dossiers de Z
			if ( $prefixe != "zcore" and $prefixe != 'z' )
				$dossiers_plugins[] = _DIR_PLUGINS.$ligne['dossier'];
			// noter quand même les repertoires de Z pour la suite
			else if ( $prefixe == 'zcore' OR $prefixe == 'z') {
				$z = $prefixe;
				$dossiers_z[] = _DIR_PLUGINS.$ligne['dossier'];
			}
		}
	}
	// on regroupe tous les dossiers
	if ( is_array($dossiers_plugins) )
		$dossiers_pages = array_merge( $dossiers_squelettes, $dossiers_plugins );
	else
		$dossiers_pages = $dossiers_squelettes;


	// 2: lister les squelettes à exclure
	// squelettes de base à exclure
	$exclure_base = array('sommaire', 'login');
	// les squelettes 'techniques' de Z
	$exclure_z = array();
	if ( $z and is_array($dossiers_z) ) {
		foreach ( $dossiers_z as $dossier ) {
			foreach ( preg_files("$dossier/contenu/" . $pattern_html) as $chemin )
				$exclure_z[] = pathinfo($chemin, PATHINFO_FILENAME);
			foreach ( preg_files("$dossier/content/" . $pattern_html) as $chemin )
				$exclure_z[] = pathinfo($chemin, PATHINFO_FILENAME);
		}
	}
	// les squelettes des objets éditoriaux
	if ( $objets_sql = lister_tables_objets_sql() and is_array($objets_sql) )
		foreach ( $objets_sql as $objet)
			$exclure_objets[] = $objet['type'];


	// 3: lister tous les squelettes dans les répertoires trouvés
	// retourne un tableau de la forme array(dossier1 => array(squelette1,squelette2), dossier2 => (...))
	$pattern_html = '[\w-=_\.]*\.html$';
	if ( is_array($dossiers_pages) ) {
		foreach ( $dossiers_pages as $dossier ) {
			// sans Z, rechercher à la racine des dossiers de squelettes
			if ( !$z ) {
				foreach ( preg_files("$dossier/" . $pattern_html) as $chemin )
					$squelettes[$dossier][] = pathinfo($chemin, PATHINFO_FILENAME);
			// avec Z, rechercher dans les sous-repertoires 'content' ou 'contenu'
			// Z 2
			} else if ($z == 'zcore') {
				foreach ( preg_files("$dossier/content/" . $pattern_html) as $chemin )
					$squelettes[$dossier.'/content'][] = pathinfo($chemin, PATHINFO_FILENAME);
			}
			// Z 1 : squelettes préfixés par "page-"
			else if ($z == 'z') {
				foreach ( preg_files("$dossier/contenu/" . $pattern_html) as $chemin )
					if ( preg_match('/^page-/',pathinfo($chemin, PATHINFO_FILENAME)) )
						$squelettes[$dossier.'/contenu'][] = preg_replace('/^page-/','',pathinfo($chemin, PATHINFO_FILENAME));
			}
		}
	}


	// 4: lister les pages en filtrant les squelettes inadéquats
	if ( is_array($squelettes) ) {
		foreach ( $squelettes as $dossier => $squelettes_dossier ) {
			foreach ( $squelettes_dossier as $squelette ) {
				$exclure = false;
				// tests par tableaux
				// liste prédéfinie, squelettes des objets, squelettes techniques
				if ( in_array($squelette, $exclure_z)
				  or in_array($squelette, $exclure_base) )
					$exclure = true;
				// tests par regex
				// squelettes commenant par "inc-"
				if ( preg_match("/^inc-/", $squelette) )
					$exclure = true;
				// 40x,30x
				if ( preg_match("/^(4|3)\d{2}$/", $squelette) )
					$exclure = true;
				// squelettes des objets éditoriaux, ex: article, article-10, article=10, article_10, article.en, article-10.en
				// cf. http://www.spip.net/fr_article3445.html
				// cf. http://plugins.spip.net/variantesarticles.html
				if ( is_array($exclure_objets) ) {
					foreach ( $exclure_objets as $objet ) {
						if ( preg_match("/^$objet((-|=|_)\d*)?((\.)[a-zA-Z]{2})?$/", $squelette) ) {
							$exclure = true;
							break;
						}
					}
				}
				if ( $exclure === false )
					$pages[$squelette] = $dossier;
			}
		}
	}

	return $pages;
}

?>
