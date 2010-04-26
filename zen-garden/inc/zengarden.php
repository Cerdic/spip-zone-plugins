<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function zengarden_charge_themes($dir = _DIR_THEMES, $tous = false){
	$themes = array();

	$files = array();
	$files = preg_files($dir,"/plugin.xml$");

	$get_infos = charger_fonction('get_infos','plugins');
	if (count($files))
		foreach($files as $file){
			$path = substr(dirname($file),strlen($dir));
			$infos = $get_infos($path,false,$dir);
			if ($infos
			  AND ($tous OR $infos['etat']=='stable')){
				$infos['chemin'] = $path;
				$infos['chemin_tri'] = strtolower($path);
				$themes[$path] = $infos;
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
