<?php
/**
 * Plugin Zen-Garden pour Spip 3.0
 * Licence GPL (c) 2006-2011 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function zengarden_affiche_version_compatible($intervalle){
	if (!strlen($intervalle)) return '';
	if (!preg_match(',^[\[\(]([0-9.a-zRC\s]*)[;]([0-9.a-zRC\s]*)[\]\)]$,',$intervalle,$regs)) return false;
	$mineure = $regs[1];
	$majeure = $regs[2];
	$mineure_inc = $intervalle{0}=="[";
	$majeure_inc = substr($intervalle,-1)=="]";
	if (strlen($mineure)){
		if (!strlen($majeure))
			$version = _T('zengarden:intitule_version') . ($mineure_inc ? ' &ge; ' : ' &gt; ') . $mineure;
		else
			$version = $mineure . ($mineure_inc ? ' &le; ' : ' &lt; ') . _T('zengarden:intitule_version') . ($majeure_inc ? ' &le; ' : ' &lt; ') . $majeure;
	}
	else {
		$version = _T('zengarden:intitule_version') . ($majeure_inc ? ' &le; ' : ' &lt; ') . $majeure;
	}

	return $version;
}

/**
 * Lister les thèmes
 *
 * @param bool $tous
 * @return array
 */
function 	zengarden_liste_themes($tous){
	include_spip('inc/zengarden');
	return zengarden_charge_themes(_DIR_THEMES,$tous);
}


?>