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
 * On ne retourne volontairement pas certains squelettes techniques, comme les 404, les inc-machin.html, etc.
 *
 * @uses verifier_fond_page_dist()
 *
 * @param string $exclure_pages_bdd
 *    Pour ne pas retourner les pages ayant des URLs enregistrées en base
 * @param string $groupby_dossier
 *    pour ranger les pages par dossier
 * @param string $filtrer_prioritaires
 *    Pour ne pas renvoyer les squelettes non prioritaires,
 *    Par exemple quand le même squelette est présent dans squelettes/ et squelettes-dist/,
 *    On ne renvoie que celui contenu dans squelettes/ car il est prioritaire.
 * @return array
 *     [page => chemin du squelette]
 *     ou avec l'option $groupby_dossier : [dossier][page => chemin du quelette]
 */
function inc_fonds_pages_to_array_dist($exclure_pages_bdd = '', $groupby_dossier = '', $filtrer_prioritaires = '') {

	// ====================================================
	// 1) Répertorier les dossiers contenant les squelettes
	// ====================================================

	// 1-1) Dossiers contenant les squelettes utilisateurs + squelettes-dist
	$dossiers_squelettes = (isset($GLOBALS['dossier_squelettes']) and strlen($GLOBALS['dossier_squelettes']) and is_array($dossiers_squelettes_glob = explode(':', $GLOBALS['dossier_squelettes']))) ?
		$dossiers_squelettes_glob :
		array("squelettes");
	$dossiers_squelettes[] = 'squelettes-dist';

	// 1-2) Dossiers des plugins actifs de catégorie "squelette" ou "outil" pour zcore.
	// On récupère le dossier de chaque plugin grâce aux colonnes "constante" et "src_archive".
	$dossiers_plugins = array();

	// A partir de spip 3.3.0, la colonne categorie a disparu dans spip_plugins
	$version = explode('-',$GLOBALS['spip_version_affichee'])[0];
	include_spip('plugins/installer');
	if (spip_version_compare($version, '3.3.0', '>=')) {
		$where = [
			'plugins.prefixe = ' . sql_quote('zcore'),
			'paquets.actif = ' . sql_quote('oui'),
		];
	} else {
		$where = [
			'(categorie = ' . sql_quote('squelette') . ' OR plugins.prefixe = ' . sql_quote('zcore') . ')',
			'paquets.actif = ' . sql_quote('oui'),
		];
	}

	if ($plugins = sql_allfetsel(
		array(
			'plugins.prefixe',
			'paquets.src_archive',
			'paquets.constante',
		),
		'spip_paquets AS paquets' .
			' INNER JOIN spip_plugins AS plugins ON plugins.id_plugin = paquets.id_plugin',
		$where
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


	// ====================================================
	// 2) Récupérer les squelettes des dossiers repertoriés
	// ====================================================
	// [NOTE] : find_all_in_path() ne renvoie pas les bonnes valeurs dans le prive (???)
	$squelettes = array();
	foreach ($dossiers_pages as $dossier){
		//$squelettes = array_merge($squelettes, find_all_in_path($dossier.'/', '\.html$'));
		$squelettes[$dossier] = preg_files($dossier.'/', '\.html$', 1000, false);
	}


	// ========================================================
	// 3) Filtrer les squelettes ne correspondant pas aux pages
	// ========================================================
	$pages = array();
	$verifier_fond_page = charger_fonction('fond_page', 'verifier');
	foreach($squelettes as $dossier => $chemins) {
		foreach($chemins as $chemin){
			$page = pathinfo($chemin, PATHINFO_FILENAME); // squelette
			$fond = pathinfo($chemin, PATHINFO_BASENAME); // squelette.html
			// Si ZPIP est activé, on retire le préfixe «page-» du nom de la page
			if ($z == 'z'
				and substr($page, 0, strlen('page-')) == 'page-'
			) {
				$type_page = substr($page, strlen('page-'));
			} else {
				$type_page = $page;
			}
			// Si nécessaire, filtrer les squelettes non prioritaires
			if ($filtrer_prioritaires
				and trouver_fond_page($type_page) !== $chemin
			) {
				$is_fond_page = false;
			}
			// Vérifier que le squelette correspond à une page
			else {
				$is_fond_page = (!strlen($verifier_fond_page($chemin, array('doublon' => $exclure_pages_bdd))));
			}
			// Si c'est bon, on l'ajoute au tableau
			if ($is_fond_page) {
				// On organise le tableau différemment en fonction de $groupby_dossier
				if (!$groupby_dossier){
					$pages[$type_page] = $chemin;
				} else {
					$pages[$dossier][$type_page] = $chemin;
				}
			}
		}
	}
	asort($pages);

	return $pages;
}
