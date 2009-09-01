<?php
/*
 * Plugin xxx
 * (c) 2009 cedric
 * Distribue sous licence GPL
 *
 */

//$GLOBALS['skin_defaut'] = 'basic'; // pour tester une skin

function get_skins(){
	static $skins = null;
	if (is_null($skins)){
		// si pas encore definie
		if (!defined('_SPIP_SKIN'))
			@define('_SPIP_SKIN','spip');
		$skins = array(_SPIP_SKIN);
		$prefs = $GLOBALS['visiteur_session']['prefs'];
		if (is_string($prefs))
			$prefs = unserialize($GLOBALS['visiteur_session']['prefs']);
		if (
			((isset($prefs['skin']) AND $skin = $prefs['skin'])
			OR (isset($GLOBALS['skin_defaut']) AND $skin = $GLOBALS['skin_defaut']))
			AND $skin != _SPIP_SKIN)
			array_unshift($skins,$skin); // placer la skin choisie en tete
	}
	return $skins;
}

function find_in_skin($file, $dirname='', $include=false){
	$skins = get_skins();
	foreach($skins as $skin){
		if ($f = find_in_path($file,"skins/$skin/$dirname",$include))
			return $f;
		// et chercher aussi comme en 2.1...
		if ($f = find_in_path($file,"prive/skins/$skin/$dirname",$include))
			return $f;		
	}
	spip_log("$dirname/$file introuvable dans la skin ".reset($skins),'skin');
	return "";
}

function find_icone($icone){
	$icone_renommer = charger_fonction('icone_renommer','inc',true);
	list($icone,$fonction) = $icone_renommer($icone,"");
	return $icone;
}

?>
