<?php

define('_COMPAT_CFG_192', true);


/* fichier de compatibilite vers spip 1.9.2 */
if (version_compare($GLOBALS['spip_version_code'], '1.9300', '<')
	AND $f = charger_fonction('compat_cfg', 'inc'))
		$f();


## ceci n'est pas l'original du plugin compat mais la copie pour CFG

// En termes de distribution ce fichier PEUT etre recopie dans chaque plugin
// qui desire en avoir une version autonome (voire forkee), A CONDITION DE
// RENOMMER le fichier et ses deux fonctions ; c'est un peu lourd a maintenir
// mais c'est le prix a payer pour l'independance des plugins entre eux :-(

// la version commune a tous est developpee sur
// svn://zone.spip.org/spip-zone/_dev_/compat/


function inc_compat_cfg_dist($quoi = NULL) {
	if (!function_exists($f = 'compat_cfg_defs')) $f .= '_dist';
	$defs = $f();

	if (is_string($quoi))
		$quoi = array($quoi);
	else if (is_null($quoi))
		$quoi = array_keys($defs);

	foreach ($quoi as $d) {
		if (!function_exists($d)
		AND isset($defs[$d])) {
			eval ("function $d".$defs[$d]);
		}
	}
}

function compat_cfg_defs_dist() {
	$defs = array();

	// http://trac.rezo.net/trac/spip/changeset/9919
	if ($GLOBALS['spip_version_code'] < '1.9259')
	$defs['sql_fetch'] = '($res, $serveur=\'\') {
		return spip_fetch_array($res);
	}';

	return $defs;
}

?>
