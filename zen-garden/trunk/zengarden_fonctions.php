<?php
/**
 * Plugin Zen-Garden pour Spip 3.0
 * Licence GPL (c) 2006-2013 Cedric Morin
 * 
 * @package SPIP\Zen-Garden\Fonctions
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
 * Les thèmes peuvent être présent dans :
 * - themes/* à la racine (ou autre _DIR_THEMES défini);
 * - squelettes/themes/*;
 * - plugins/*;
 *
 * @param bool $tous
 * @return array
 */
function zengarden_liste_themes($tous){
	include_spip('inc/zengarden');

	$themes = array();

	// charger les themes de themes-dist/
	if (is_dir(_DIR_THEMES_DIST))
		$themes = array_merge($themes, zengarden_charge_themes(_DIR_THEMES_DIST, $tous));

	// charger les themes de themes/
	if (is_dir(_DIR_THEMES))
		$themes = array_merge($themes, zengarden_charge_themes(_DIR_THEMES, $tous));

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

	/**
	 * Recherche spécifique
	 * Invalider les thèmes incompatibles
	 * 
	 * Si le squelette ou un plugin définit la constante _ZENGARDEN_FILTRE_THEMES, 
	 * on ne prend que les thèmes compatibles
	 * Sinon, si on a le plugin zpip-dist, on ne liste que les thèmes compatibles avec zpip-dist
	 * 
	 * Pour être compatible un thème doit avoir un <utilise...> du squelette en question dans son paquet.xml
	 */
	$search = "dist";
	if (defined('_ZENGARDEN_FILTRE_THEMES')) $search=_ZENGARDEN_FILTRE_THEMES;
	elseif (defined('_DIR_PLUGIN_ZPIP')) $search="zpip";
	elseif (defined('_DIR_PLUGIN_Z')) $search="z";
	
	if ($search){
		foreach ($themes as $k => $theme){
			$keep = false;
			foreach ($theme['utilise'] as $u){
				if (strncasecmp($u['nom'],$search,max(strlen($u['nom']),strlen($search)))==0){
					$keep = true;
					continue;
				}
			}
			if (!$keep)
				unset($themes[$k]);
		}
	}

	// et voila
	return $themes;
}

/**
 * Insertion dans le pipeline filter_liste_plugins (SPIP)
 * 
 * Enlève les thèmes de la liste des plugins dans le privé
 * 
 * @param array $flux 
 * 		 Le tableau de la liste des plugins
 * @return array $flux 
 * 		 Le tableau de la liste des plugins modifié
 */
function zengarden_filtrer_liste_plugins($flux){
	foreach($flux['data'] as $d=>$info){
		if ($info['categorie']=='theme'){
			unset($flux['data'][$d]);
		}
	}
	return $flux;
}



/**
 * Afficher les auteurs ou licences
 *
 * Vient de plugin.xml ou paquet.xml
 * 
 * @param array $donnees
 * @return string
**/
function zengarden_affiche_info($donnees) {
	if (is_array($donnees) AND count($donnees)) {
		$liste = array();
		foreach ($donnees as $d) {
			if (!is_array($d)) {
				$liste[] = $d;
			} else {
				$liste[] = $d['nom'];
			}
		}
		return implode(',', $liste);
	}
	return '';
}

?>
