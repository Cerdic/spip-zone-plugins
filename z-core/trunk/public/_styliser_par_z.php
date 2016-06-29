<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 * \***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Fonction Page automatique a partir de contenu/xx
 *
 * @param array $flux
 *
 * @return array
 */
function public_styliser_par_z_dist($flux) {
	static $prefix_path = null;
	static $prefix_length;
	static $z_blocs;
	static $apl_constant;
	static $page;
	static $disponible = array();
	static $echafauder;
	static $prepend = "";

	if (!isset($prefix_path)) {
		$z_blocs = zcore_blocs(test_espace_prive());
		if (test_espace_prive()) {
			$prefix_path = "prive/squelettes/";
			$prefix_length = strlen($prefix_path);
			$apl_constant = '_ECRIRE_AJAX_PARALLEL_LOAD';
			$page = 'exec';
			$echafauder = ""; /* pas d'echafaudage dans ecrire/ pour le moment */
			define('_ZCORE_EXCLURE_PATH', '');
		} else {
			$prefix_path = "";
			$prefix_length = 0;
			$apl_constant = '_Z_AJAX_PARALLEL_LOAD';
			$page = _SPIP_PAGE;
			$echafauder = charger_fonction('echafauder', 'public', true);
			define('_ZCORE_EXCLURE_PATH', 'squelettes-dist|prive');
		}
		$prepend = (defined('_Z_PREPEND_PATH') ? _Z_PREPEND_PATH : "");
	}
	$z_contenu = reset($z_blocs); /* contenu par defaut */

	$fond = $flux['args']['fond'];
	if ($prepend OR strncmp($fond, $prefix_path, $prefix_length) == 0) {
		$fond = substr($fond, $prefix_length);
		$squelette = $flux['data'];
		$ext = $flux['args']['ext'];

		/* Ajax Parallel loading : ne pas calculer le bloc, mais renvoyer un js qui le loadera en ajax */
		if (defined('_Z_AJAX_PARALLEL_LOAD_OK') AND $dir = explode('/', $fond) AND count($dir) == 2 /* pas un sous repertoire */ AND $dir = reset($dir) AND in_array($dir, $z_blocs) /* verifier deja qu'on est dans un bloc Z*/ AND in_array($dir, explode(',', constant($apl_constant))) /* et dans un demande en APL */ AND $pipe = zcore_trouver_bloc($prefix_path . $prepend, $dir, 'z_apl', $ext) /* et qui contient le squelette APL */) {
			$flux['data'] = $pipe;

			return $flux;
		}

		/* surcharger aussi les squelettes venant de squelettes-dist/ */
		if ($squelette AND !zcore_fond_valide($squelette)) {
			$squelette = "";
			$echafauder = "";
		}
		if ($prepend) {
			$squelette = substr(find_in_path($prefix_path . $prepend . "$fond.$ext"), 0, -strlen(".$ext"));
			if ($squelette) {
				$flux['data'] = $squelette;
			}
		}

		/* gerer les squelettes non trouves
		 * -> router vers les /dist.html
		 * ou scaffolding ou page automatique les contenus
		 */
		if (!$squelette) {

			// si on est sur un ?page=XX non trouve
			if ($flux['args']['contexte'][$page] == $fond OR $flux['args']['contexte']['type'] == $fond OR ($fond == 'sommaire' AND !$flux['args']['contexte'][$page])) {

				/* si on est sur un ?page=XX non trouve
				 * se brancher sur contenu/xx si il existe
				 * ou si c'est un objet spip, associe a une table, utiliser le fond homonyme
				*/
				if (!isset($disponible[$fond])) {
					$disponible[$fond] = zcore_contenu_disponible($prefix_path . $prepend, $z_contenu, $fond, $ext, $echafauder);
				}

				if ($disponible[$fond]) {
					$flux['data'] = substr(find_in_path($prefix_path . "page.$ext"), 0, -strlen(".$ext"));
				}
			} /* echafaudage :
			 * si c'est un fond de contenu d'un objet en base
			 * generer un fond automatique a la volee pour les webmestres
			*/ elseif (strncmp($fond, "$z_contenu/", strlen($z_contenu) + 1) == 0 AND include_spip('inc/autoriser') AND isset($GLOBALS['visiteur_session']['statut']) /* performance */ AND autoriser('webmestre')) {
				$type = substr($fond, strlen($z_contenu) + 1);
				if (!isset($disponible[$type])) {
					$disponible[$type] = zcore_contenu_disponible($prefix_path . $prepend, $z_contenu, $type, $ext, $echafauder);
				}
				if (is_string($disponible[$type])) {
					$flux['data'] = $disponible[$type];
				} elseif ($echafauder AND $is = $disponible[$type] AND is_array($is)) {
					$flux['data'] = $echafauder($type, $is[0], $is[1], $is[2], $ext);
				}
			} /* sinon, si on demande un fond non trouve dans un des autres blocs
			 * et si il y a bien un contenu correspondant ou echafaudable
			 * se rabbatre sur le dist.html du bloc concerne
			 */ else {
				if ($dir = explode('/', $fond) AND $dir = reset($dir) AND $dir !== $z_contenu AND in_array($dir, $z_blocs)) {
					$type = substr($fond, strlen("$dir/"));
					if ($type !== 'page' AND !isset($disponible[$type])) {
						$disponible[$type] = zcore_contenu_disponible($prefix_path . $prepend, $z_contenu, $type, $ext, $echafauder);
					}
					if ($type == 'page' OR $disponible[$type]) {
						$flux['data'] = zcore_trouver_bloc($prefix_path . $prepend, $dir, 'dist', $ext);
					}
				}
			}
			$squelette = $flux['data'];
		}
		/* layout specifiques par type et compositions :
		 * body-article.html
		 * body-sommaire.html
		 * pour des raisons de perfo, les declinaisons doivent etre dans le
		 * meme dossier que body.html
 		 */
		if ($fond == 'body' AND substr($squelette, -strlen($fond)) == $fond) {
			if (isset($flux['args']['contexte']['type']) AND ((isset($flux['args']['contexte']['composition']) AND file_exists(($f = $squelette . "-" . $flux['args']['contexte']['type'] . "-" . $flux['args']['contexte']['composition']) . ".$ext")) OR file_exists(($f = $squelette . "-" . $flux['args']['contexte']['type']) . ".$ext"))) {
				$flux['data'] = $f;
			}
		} elseif ($fond == 'structure' AND _request('var_zajax') AND $f = find_in_path($prefix_path . $prepend . 'ajax' . ".$ext")) {
			$flux['data'] = substr($f, 0, -strlen(".$ext"));
		} /* chercher le fond correspondant a la composition */ elseif (isset($flux['args']['contexte']['composition']) AND (basename($fond) == 'page' OR ($squelette AND substr($squelette, -strlen($fond)) == $fond)) AND $dir = substr($fond, $prefix_length) AND $dir = explode('/', $dir) AND $dir = reset($dir) AND in_array($dir, $z_blocs) AND $f = find_in_path($prefix_path . $prepend . $fond . "-" . $flux['args']['contexte']['composition'] . ".$ext")) {
			$flux['data'] = substr($f, 0, -strlen(".$ext"));
		}
	}

	return $flux;
}

/**
 * Lister les blocs de la page selon le contexte prive/public
 *
 * @param bool $espace_prive
 *
 * @return array
 */
function zcore_blocs($espace_prive = false) {
	if ($espace_prive) {
		return (isset($GLOBALS['z_blocs_ecrire']) ? $GLOBALS['z_blocs_ecrire'] : array(
			'contenu',
			'navigation',
			'extra',
			'head',
			'hierarchie',
			'top',
		));
	}

	return (isset($GLOBALS['z_blocs']) ? $GLOBALS['z_blocs'] : array('content'));
}

/**
 * Verifier qu'un type a un contenu disponible,
 * soit parcequ'il a un fond, soit parce qu'il est echafaudable
 *
 * @param string $prefix_path
 * @param string $z_contenu
 * @param string $type
 * @param string $ext
 *
 * @return mixed
 */
function zcore_contenu_disponible($prefix_path, $z_contenu, $type, $ext, $echafauder = true) {
	if ($d = zcore_trouver_bloc($prefix_path, $z_contenu, $type, $ext)) {
		return $d;
	}

	return $echafauder ? zcore_echafaudable($type) : false;
}

function zcore_fond_valide($squelette) {
	if (!_ZCORE_EXCLURE_PATH OR !preg_match(',(' . _ZCORE_EXCLURE_PATH . ')/,', $squelette)) {
		return true;
	}

	return false;
}

/**
 * Trouver un bloc qui peut etre sous le nom
 * contenu/article.html
 * ou
 * contenu/contenu.article.html
 *
 * @param string $prefix_path
 *    chemin de base qui prefixe la recherche
 * @param string $bloc
 *    nom du bloc cherche
 * @param string $fond
 *    nom de la page (ou 'dist' pour le bloc par defaut)
 * @param <type> $ext
 *    extension du squelette
 *
 * @return string
 */
function zcore_trouver_bloc($prefix_path, $bloc, $fond, $ext) {
	if (($f = find_in_path("$prefix_path$bloc/$bloc.$fond.$ext") AND zcore_fond_valide($f)) OR ($f = find_in_path("$prefix_path$bloc/$fond.$ext") AND zcore_fond_valide($f))) {
		return substr($f, 0, -strlen(".$ext"));
	}

	return "";
}

/**
 * Tester si un type est echafaudable
 * cad si il correspond bien a un objet en base
 *
 * @staticvar array $echafaudable
 *
 * @param string $type
 *
 * @return bool
 */
function zcore_echafaudable($type) {
	static $echafaudable = array();
	if (isset($echafaudable[$type])) {
		return $echafaudable[$type];
	}
	if (preg_match(',[^\w],', $type)) {
		return $echafaudable[$type] = false;
	}
	if ($table = table_objet($type) AND $type == objet_type($table) AND $trouver_table = charger_fonction('trouver_table', 'base') AND ($desc = $trouver_table($table) OR $desc = $trouver_table($table_sql = "spip_$table"))) {
		return $echafaudable[$type] = array($table, $desc['table'], $desc);
	} else {
		return $echafaudable[$type] = false;
	}
}

