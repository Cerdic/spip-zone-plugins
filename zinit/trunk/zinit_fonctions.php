<?php

/**
 * Fonctions utiles au plugin Initialiser Zcore.
 *
 * @plugin     Initialiser Zcore
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Avoir la liste des blocs/répertoires pour zcore.
 *
 * @return array
 *         Tableau des blocs définies par la globale 'z_blocs' ou une liste par défaut.
 */
function zinit_repertoire_skel_defaut() {
	// Si la globale 'z_blocs' n'est pas définie,
	// on va donner une liste de dossiers par défaut.
	if (!isset($GLOBALS['z_blocs'])) {
		$repertoires = array('content', 'head', 'head_js');
	} else {
		// Sinon, on prend les valeurs données par 'z_blocs'.
		$repertoires = $GLOBALS['z_blocs'];
	}

	return $repertoires;
}

/**
 * Cette fonction va créer les répertoires pour le plugin.
 */
function zinit_repertoire_skel_creer($repertoire_zcore = _ZINIT_DIR_SQUELETTES) {
	$repertoires = zinit_repertoire_skel_defaut();
	if (is_null($repertoire_zcore) or empty($repertoire_zcore)) {
		$repertoire_zcore = _ZINIT_DIR_SQUELETTES;
	}
	foreach ($repertoires as $repertoire) {
		if (!is_dir($repertoire_zcore . $repertoire)) {
			@mkdir($repertoire_zcore . $repertoire, _SPIP_CHMOD, true);
		}
	}

	return true;
}

/**
 * Mettre à jour la liste des répertoires présents dans 'squelettes_zcore'.
 * Si des répertoires ne font plus partis des répertoires de la globale 'z_blocs',
 * ces répertoires seront supprimés s'ils sont vides.
 */
function zinit_repertoire_skel_maj($repertoire_zcore = _ZINIT_DIR_SQUELETTES) {
	$repertoires_defaut = zinit_repertoire_skel_defaut();
	$black_list = array('..', '.', '.svn', '.DS_Store');
	if (is_null($repertoire_zcore) or empty($repertoire_zcore)) {
		$repertoire_zcore = _ZINIT_DIR_SQUELETTES;
	}

	if (is_dir($repertoire_zcore)) {
		// On liste les répertoires qui ont été créés dans squelettes_zcore
		$repertoires_crees = array_diff(scandir($repertoire_zcore), $black_list);
		// On ne garde que les répertoires qui ne font plus partis des répertoires nécessaires à zcore.
		$repertoires_obsoletes = array_diff($repertoires_crees, $repertoires_defaut);
		if (is_array($repertoires_obsoletes) and count($repertoires_obsoletes) > 0) {
			// On a bien une liste de répertoires obsolètes,
			// alors, on les efface s'ils sont vides.
			foreach ($repertoires_obsoletes as $repertoire) {
				if (is_dir($repertoire_zcore . $repertoire) and (count(array_diff(scandir($repertoire_zcore . $repertoire), $black_list)) == 0)) {
					@rmdir($repertoire_zcore . $repertoire);
				}
			}
		}
	}

	return;
}

/**
 * Lister les répertoires de squelettes_zcore.
 *
 * @return bool|array
 *         `false` : si le répertoire 'squelettes_zcore' n'a pas été créé.
 *         `array` : liste des répertoires.
 */
function zinit_repertoire_skel_lister($repertoire_zcore = _ZINIT_DIR_SQUELETTES) {
	$repertoires = array();
	if (is_null($repertoire_zcore) or empty($repertoire_zcore)) {
		$repertoire_zcore = _ZINIT_DIR_SQUELETTES;
	}
	// On crée les répertoires.
	zinit_repertoire_skel_creer();
	// On vérifie que $repertoire passé en paramètre est bien un répertoire existant.
	// cf. ../IMG/orphelins qui ne serait pas encore créé.
	if (is_dir($repertoire_zcore)) {
		// Avec la fonction scandir, on liste le contenu (existant) du répertoire cible.
		$repertoires_tmp = array_diff(scandir($repertoire_zcore), array(
			'..',
			'.',
			'.svn',
			'.DS_Store',
		)); // On ne liste pas le répertoire .svn
		foreach ($repertoires_tmp as $repertoire) {
			// On vérifie que c'est un répertoire et non un fichier.
			if (is_dir($repertoire_zcore . $repertoire)) {
				$repertoires[] = $repertoire_zcore . $repertoire;
			}
		}
	} else {
		return false;
	}

	return (array) $repertoires;
}

/**
 * On liste toutes les tables qui ont une page de vue déterminée.
 *
 * @return array Liste des tables.
 */
function zinit_lister_tables() {
	include_spip('base/objets');

	$tables_a_exclure = $GLOBALS['zinit_tables_exclues'];
	$tables_objets_sql = lister_tables_objets_sql();

	// *****
	// On nettoie les tables
	// *****
	// On exclu les tables que l'on ne désire pas avoir
	foreach ($tables_a_exclure as $table_exclue) {
		unset($tables_objets_sql[$table_exclue]);
	}
	foreach ($tables_objets_sql as $table => $champs) {
		// Si l'objet n'a pas de page de vue pour le public,
		// alors on ne garde pas cet objet pour zinit
		if (is_null($champs['page']) or empty($champs['page'])) {
			unset($tables_objets_sql[$table]);
		}
	}

	return $tables_objets_sql;
}

function zinit_template_skel_creer($cible = _ZINIT_DIR_SQUELETTES) {
	$objets = zinit_lister_tables();
	if (is_null($cible) or empty($cible)) {
		$cible = _ZINIT_DIR_SQUELETTES;
	}

	foreach ($objets as $table_sql => $descriptif) {
		$contexte = array();
		$contexte['objet'] = objet_type($table_sql);
		$contexte['id_secteur'] = (isset($descriptif['field']['id_secteur']) ? true : false);
		$contexte['id_rubrique'] = (isset($descriptif['field']['id_rubrique']) ? true : false);
		$content = recuperer_fond('inclure/objet_template', $contexte);
		$template = fopen($cible . $contexte['objet'] . '.html', 'w+') or die('Unable to open file!');
		fwrite($template, $content);
		fclose($template);
	}

	return true;
}

/**
 * Vérifier que la globale 'z_blocs' existe.
 *
 * @return bool
 *         true : la globale est renseignée et est un tableau
 *         false : la globale n'existe pas et/ou n'est pas un tableau.
 */
function zinit_blocs_verifier() {
	if (isset($GLOBALS['z_blocs']) and is_array($GLOBALS['z_blocs'])) {
		return true;
	} else {
		return false;
	}
}

function zinit_check_skel_dist() {
	$dir_skel_dist = _DIR_SQUELETTES_DIST;
	$fichiers = array();
	if (is_dir($dir_skel_dist) and is_readable($dir_skel_dist)) {
		$contenus_skel_dist = array_diff(scandir($dir_skel_dist), array(
			'..',
			'.',
			'.svn',
			'.DS_Store',
		)); // On ne liste pas le répertoire ^.
		foreach ($contenus_skel_dist as $fichier) {
			if (preg_match("/"._EXTENSION_SQUELETTES."$/", $fichier)) {
				$fichiers[] = $fichier;
			}
		}
	}

	return $fichiers;
}