<?php
/**
 * Itérateur « pages » du plugin URLs Pages Personnalisées
 *
 * @plugin     URLs Pages Personnalisées
 * @copyright  2016
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package
 */


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Renvoie une liste des pages et le chemin de leurs squelettes.
 *
 * Les pages sont les squelette qui ne correspondent à aucun objet éditorial.
 * On cherche dans les dossiers squelettes utilisateur et dist, et ceux des plugins de squelettes activés.
 *
 * On ne retourne volontairement pas certains squelettes, cf. valider_fond_page()
 *
 * @uses valider_fond_page()
 *
 * @param string $exclure_pages_bdd
 *    Pour ne pas retourner les pages ayant des URLs enregistrées en base
 * @param string $groupby_dossier
 *    pour ranger les pages par dossier
 * @return array
 *     [page => chemin du squelette]
 *     ou avec l'option $groupby_dossier : [dossier][page => chemin du quelette]
 */
function inc_fonds_pages_to_array_dist($exclure_pages_bdd = '', $groupby_dossier = '') {

	// ================================================
	// 1) Trouver les dossiers contenant les squelettes
	// ================================================

	// 1-1) Dossiers contenant les squelettes utilisateurs + squelettes-dist
	$dossiers_squelettes = (isset($GLOBALS['dossier_squelettes']) and strlen($GLOBALS['dossier_squelettes']) and is_array($dossiers_squelettes_glob = explode(':', $GLOBALS['dossier_squelettes']))) ?
		$dossiers_squelettes_glob :
		array("squelettes");
	$dossiers_squelettes[] = 'squelettes-dist';

	// 1-2) Dossiers des plugins actifs de catégorie "squelette" ou "outil" pour zcore.
	// On récupère le dossier de chaque plugin grâce aux colonnes "constante" et "src_archive".
	$dossiers_plugins = array();
	if ($plugins = sql_allfetsel(
		array(
			'plugins.prefixe',
			'paquets.src_archive',
			'paquets.constante',
		),
		'spip_paquets AS paquets' .
			' INNER JOIN spip_plugins AS plugins ON plugins.id_plugin = paquets.id_plugin',
		array (
			'(categorie = ' . sql_quote('squelette') . ' OR plugins.prefixe = ' . sql_quote('zcore') . ')',
			'paquets.actif = ' . sql_quote('oui'),
		)
	)){
		foreach ($plugins as $plugin){
			// Noter tous les dossiers, sauf ceux de zcore
			// (zcore ne contient que des squelettes techniques ou d'objets éditoriaux)
			$prefixe = strtolower($plugin['prefixe']);
			if ($prefixe != 'zcore') {
				$dossier_plugin = preg_replace('/\.\.\//', '', constant($plugin['constante']));
				$dossiers_plugins[] = $dossier_plugin.  $plugin['src_archive'];
			}
			// Poser un flag si zcore/zpip est actif, pour ajouter les sous-dossiers content/contenu plus tard
			if (in_array($prefixe, array('z', 'zcore'))) {
				$z = $prefixe;
			}
		}
	}

	// 1-3) On regroupe ensemble les dossiers des squelettes et des plugins
	$dossiers_pages = array_merge($dossiers_squelettes, $dossiers_plugins);

	// 1-4) Si zcore/zpip est actif, on ajoute les sous dossiers content/contenu
	if (isset($z)) {
		$mapping_sous_dossier_z = array(
			'zcore' => 'content',
			'z'     => 'contenu',
		);
		$sous_dossier_z = $mapping_sous_dossier_z[$z]; // content ou contenu
		foreach ($dossiers_pages as $dossier){
			$dossiers_pages[] = "$dossier/$sous_dossier_z";
		}
	}

	// [FIXME] 1.5) Selon le contexte prive/public, le chemin doit commencer par "../" ou non,
	// auquel cas preg_files() plante (???)
	foreach ($dossiers_pages as $k => $dossier){
		// prive : "../"
		if (test_espace_prive()
			and !preg_match('/\.\.\//', $dossier)
		) {
			$dossiers_pages[$k] = '../' . $dossiers_pages[$k];
		// public : pas de "../"
		} elseif (!test_espace_prive()
			and preg_match('/\.\.\//', $dossier)
		) {
			$dossiers_pages[$k] = substr(3, $dossiers_pages[$k]);
		}
	}
	asort($dossiers_pages);


	// =====================================================
	// 2) Trouver les squelettes des pages dans ces dossiers
	// =====================================================


	// 2.1) On récupère tous les squelettes
	// ------------------------------------
	// [NOTE] : find_all_in_path() ne renvoie pas les bonnes valeurs dans le prive (???)
	$squelettes = array();
	foreach ($dossiers_pages as $dossier){
		//$squelettes = array_merge($squelettes, find_all_in_path($dossier.'/', '\.html$'));
		$squelettes[$dossier] = preg_files($dossier.'/', '\.html$', 1000, false);
	}


	// 2.2) Puis on filtre les squelettes inadéquats
	// -----------------------------------------------------------------------
	$pages = array();
	foreach($squelettes as $dossier => $chemins) {
		foreach($chemins as $chemin){
			$page = pathinfo($chemin, PATHINFO_FILENAME); // squelette
			$fond = pathinfo($chemin, PATHINFO_BASENAME); // squelette.html
			$fond_valide = valider_chemin_page($chemin, $z, $exclure_pages_bdd);
			if ($fond_valide) {
				// On organise le tableau différemment en fonction de $groupby_dossier
				if (!$groupby_dossier){
					$pages[$page] = $chemin;
				} else {
					$pages[$dossier][$page] = $chemin;
				}
			}
		}
	}
	asort($pages);

	return $pages;
}


/**
 * Déterminer si une page est valide, d'après le chemin de son squelette
 *
 * Squelettes non valides :
 * - squelettes techniques : sommaire.html, backend.html, inc-xxx.html, sitemap.xml.html, etc.
 * - squelettes des erreurs HTTP : 404, 403 etc.
 * - squelettes des objets éditoriaux
 * - squelettes dans le privé
 *
 * @param string $chemin
 *     chemin/vers/page.html
 * @param string $z
 *     'zcore' si zcore est activé
 *     'z'     si zpip est activé
 * @param boolean | string  $no_bdd
 *     Pour exclure les pages en BDD
 * @return bool
 */
function valider_chemin_page($chemin, $z = '', $no_bdd = false) {

	$is_valide = false; // valeur de retour par défaut
	$page = pathinfo($chemin, PATHINFO_FILENAME); // squelette
	$fond = pathinfo($chemin, PATHINFO_BASENAME); // squelette.html

	// 1) D'abord on prépare les filtres (listes ou masques)
	// =====================================================

	// 1.1) Masque : pas un objet éditorial
	// Il faut prendre en compte les déclinaisons (http://spip.net/3445)
	// - objet.html : la base
	// - objet.lg.html : pour une langue donnée (+ combinaisons : objet=N.lg.html, etc.)
	// - objet=N.html : pour une rubrique N
	// - objet-N.html : pour une branche N
	// - objet_N.html : pour un numéro N précis (plugin variantes articles)
	// - objet_composition.html : pour une composition précise (plugin compositions)
	// Listons les objets et leurs surnoms
	$objets = lister_objets_types();
	// Pour zpip, les squelettes sont préfixés par "page-"
	if ($z == 'z'){
		$objets = array_map(function($v){return "page-$v";}, $objets);
	}
	$objets_split = join('|', $objets);
	$exclure_regex_objets = "/^($objets_split)([\-=_][a-zA-Z0-9]+)?(\.[a-z]{2})?\.html$/";

	// 1.2) Liste : pas dans un liste prédéfinie de squelettes avec les noms exacts (sans extension .html car on est fainéants)
	$exclure_predefinis = array('sommaire', 'login', 'backend', 'distrib', 'ical', 'structure', 'body', 'z_apl');
	$exclure_predefinis = array_map(function($v){return "$v.html";}, $exclure_predefinis);

	// 1.3) Masque : pas un squelette technique commençant par certaines chaînes (^squelette)
	$exclure_misc = array('inc-', 'backend-', 'rss_forum_');
	$exclure_misc_split = join('|', $exclure_misc);
	$exclure_regex_misc = "/^($exclure_misc_split)/";

	// 1.4) Masque : pas un code HTTP 404.html et cie
	$exclure_regex_http = '/^[0-9]{3}\.html$/';

	// 1.5) Masque : pas un pseudo-squelette fichier : squelette.txt.html, squelette.xml.html etc.
	$exclure_regex_fichier = '/\.[a-z]{3}\.html$/';

	// 1.6) Liste : pas une page enregistrée en base
	if ($no_bdd) {
		$pages_bdd = sql_allfetsel('page', 'spip_urls', 'page != \'\'');
		$exclure_pages_bdd = array_map(function($v){return $v['page'];}, $pages_bdd);
	} else {
		$exclure_pages_bdd = array();
	}


	// 2) Puis on applique les filtres
	// ===============================

	$not_in_bdd       = (!in_array($page, $exclure_pages_bdd)); // pas en bdd
	$not_in_liste     = (!in_array($fond, $exclure_predefinis)); // pas dans une liste prédéfinie
	$not_in_prive     = (!preg_match('/^prive/', $chemin)); // pas dans prive
	$no_match_objet   = (!preg_match($exclure_regex_objets, $fond)); // pas un objet éditorial
	$no_match_misc    = (!preg_match($exclure_regex_misc, $fond)); // pas dans une liste prédéfinie avec masque
	$no_match_http    = (!preg_match($exclure_regex_http, $fond)); // pas un code HTTP 404 et cie
	$no_match_fichier = (!preg_match($exclure_regex_fichier, $fond)); // pas un pseudo fichiers .xml et cie

	if ($not_in_bdd
		and $not_in_prive
		and $no_match_objet
		and $not_in_liste
		and $no_match_misc
		and $no_match_http
		and $no_match_fichier
	) {
		$is_valide = true;
	}

	return $is_valide;
}
