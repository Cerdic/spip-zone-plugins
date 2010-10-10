<?php
/*
 * Plugin Bando
 * (c) 2009 cedric
 * Distribue sous licence GPL
 *
 */

#$GLOBALS['theme_prive_defaut'] = 'basic'; // pour tester un theme

function lister_themes_prives(){
	static $themes = null;
	if (is_null($themes)){
		// si pas encore definie
		if (!defined('_SPIP_THEME_PRIVE'))
			@define('_SPIP_THEME_PRIVE','spip');
		$themes = array(_SPIP_THEME_PRIVE);
		$prefs = $GLOBALS['visiteur_session']['prefs'];
		if (is_string($prefs))
			$prefs = unserialize($GLOBALS['visiteur_session']['prefs']);
		if (
			((isset($prefs['theme']) AND $theme = $prefs['theme'])
			OR (isset($GLOBALS['theme_prive_defaut']) AND $theme = $GLOBALS['theme_prive_defaut']))
			AND $theme != _SPIP_THEME_PRIVE)
			array_unshift($themes,$theme); // placer le theme choisie en tete
	}
	return $themes;
}

function find_in_theme($file, $dirname='', $include=false){
	$themes = lister_themes_prives();
	foreach($themes as $theme){
		if ($f = find_in_path($file,"prive/themes/$theme/$dirname",$include))
			return $f;
	}
	// fall back transitoire sur prive/images/
	if ($f = find_in_path($file,"",$include))
		return $f;
	spip_log("$dirname/$file introuvable dans le theme ".reset($themes),'theme');
	return "";
}

function find_icone($icone){
	$icone_renommer = charger_fonction('icone_renommer','inc',true);
	list($icone,$fonction) = $icone_renommer($icone,"");
	return $icone;
}

if (
	(!isset($GLOBALS['visiteur_session']['statut']) OR $GLOBALS['visiteur_session']['statut']!=='0minirezo')
	AND strlen($GLOBALS['meta']['adresses_secondaires'])
	){
	$u = $_SERVER['HTTP_HOST'] . self('&',true);
	$as = explode("\n",$GLOBALS['meta']['adresses_secondaires']);
	foreach($as as $a)
		if (strncmp($u,$a,strlen($a))==0){
			include_spip('inc/filtres_mini');
			$u = url_absolue(self(),$GLOBALS['meta']['adresse_site']);
			include_spip('inc/headers');
			redirige_par_entete($u, '', 301);
		}
}

?>
