<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function zengarden_charge_themes($dir = _DIR_THEMES, $tous = false){
	$themes = array();

	$files = preg_files($dir,"/plugin.xml$");

	if (count($files)) {
		$get_infos = charger_fonction('get_infos','plugins');
		foreach($files as $k=>$file){
			$files[$k] = substr(dirname($file),strlen($dir));
		}

		$themes = $get_infos($files,false,$dir);

		foreach($themes as $dir=>$info){
			if ($info['categorie']!='theme'
			  OR (!$tous AND $info['etat']!=='stable'))
				unset($themes[$dir]);
			else
				$themes[$dir]['tri'] = strtolower($dir);
		}
	}
	return $themes;
}

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

?>
