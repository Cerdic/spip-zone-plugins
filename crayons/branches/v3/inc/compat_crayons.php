<?php
/**
 * Crayons
 * plugin for spip
 * (c) Fil, toggg 2006-2013
 * licence GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

## ceci n'est pas l'original mais la copie pour crayons

// Module de compatibilite pour les plugins qui veulent passer en 1.9.3
// tout en restant compatibles avec 1.9.2 voire 1.9.1 : il permet de faire
// tourner sur ces versions du code prevu pour 1.9.3

// C'est l'inverse de vieilles_defs (lequel vise a permettre a du code
// fait pour 1.9.2 de tourner sur 1.9.3)

// Ce module doit etre appele par le plugin ("nouveau code") de la
// maniere suivante :
/*

// Si SPIP est vieux, charger les fonctions de compat
if ($GLOBALS['spip_version_code'] < '1.93'
AND $f = charger_fonction('compat', 'inc'))
	$f();

qui charge toutes les defs de compat connues

ou plus precis :

// Si SPIP est vieux, charger les fonctions de compat
if ($GLOBALS['spip_version_code'] < '1.93'
AND $f = charger_fonction('compat', 'inc'))
	$f('sql_fetch');

ou encore :

// Si SPIP est vieux, charger les fonctions de compat
if ($GLOBALS['spip_version_code'] < '1.93'
AND $f = charger_fonction('compat', 'inc'))
	$f(array('sql_fetch', '_q'));


*/

// En termes de distribution ce fichier PEUT etre recopie dans chaque plugin
// qui desire en avoir une version autonome (voire forkee), A CONDITION DE
// RENOMMER le fichier et ses deux fonctions ; c'est un peu lourd a maintenir
// mais c'est le prix a payer pour l'independance des plugins entre eux :-(

// la version commune a tous est developpee sur
// svn://zone.spip.org/spip-zone/_dev_/compat/


function inc_compat_crayons_dist($quoi = null) {
	if (!function_exists($f = 'compat_crayons_defs')) {
		$f .= '_dist';
	}
	$defs = $f();

	if (is_string($quoi)) {
		$quoi = array($quoi);
	} elseif (is_null($quoi)) {
		$quoi = array_keys($defs);
	}

	foreach ($quoi as $d) {
		if (!function_exists($d) and isset($defs[$d])) {
			eval("function $d". $defs[$d]);
		}
	}
}

function compat_crayons_defs_dist() {
	$defs = array();

	// http://trac.rezo.net/trac/spip/changeset/9919
	if ($GLOBALS['spip_version_code'] < '1.9259') {
		$defs['sql_fetch'] = '($res, $serveur=\'\') {
			return spip_fetch_array($res);
		}';
	}

	$defs['table_objet_sql'] = '($type) {
		global $table_des_tables;
		$nom = table_objet($type);
		include_spip(\'public/interfaces\');
		if (isset($table_des_tables[$nom])) {
			$t = $table_des_tables[$nom];
			$nom = \'spip_\' . $t;
		}
		return $nom ;
	}';

	// Contourner un bug du plugin CFG
	include_spip('base/abstract_sql');

	return $defs;
}
