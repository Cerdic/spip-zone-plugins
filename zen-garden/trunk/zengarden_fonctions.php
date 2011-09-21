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
 * Lister les th�mes
 *
 * @param bool $tous
 * @return array
 */
function 	zengarden_liste_themes($tous){
	include_spip('inc/zengarden');

	$themes = array();

	// charger les themes de themes/
	if (is_dir(_DIR_THEMES))
		$themes = array_merge($themes,zengarden_charge_themes(_DIR_THEMES,$tous));

	// ceux de squelettes/themes/
	if (is_dir($skels=_DIR_RACINE."squelettes/themes/"))
		$themes = array_merge($themes,zengarden_charge_themes($skels,$tous));

	// ceux de chaque  dossier_squelettes/themes/
	if (strlen($GLOBALS['dossier_squelettes'])){
		$s = explode(":",$GLOBALS['dossier_squelettes']);
		foreach($s as $d){
			if (_DIR_RACINE AND strncmp($d,_DIR_RACINE,strlen(_DIR_RACINE))!==0)
				$d = _DIR_RACINE . $d;
			if (is_dir($f="$d/themes/") AND $f!=$skels)
				$themes = array_merge($themes,zengarden_charge_themes($f,$tous));
		}
	}

	// ceux de plugins/
	$themes = array_merge($themes,zengarden_charge_themes(_DIR_PLUGINS,$tous));

	// et voila
	return $themes;
}


function zengarden_filtrer_liste_plugins($flux){
	foreach($flux['data'] as $d=>$info){
		if ($info['categorie']=='theme'){
			unset($flux['data'][$d]);
		}
	}
	return $flux;
}

?>