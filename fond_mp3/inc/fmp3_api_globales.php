<?php

// inc/fmp3_api_globales.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Fmp3.
	
	Fmp3 is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Fmp3 is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Fmp3; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Fmp3. 
	
	Fmp3 est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Fmp3 est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en même temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/utils');

/**
 * si reseau local, activer le log de dev
 * Vous pouvez forcer l'option en placant define("_FMP3_DEBUG", true) dans *_options.php
 */
if(!defined("_FMP3_DEBUG")) {
	define("_FMP3_DEBUG", preg_match('/^(192\.168|127\.0)/', $_SERVER['SERVER_ADDR']));
}

function fmp3_spip_est_inferieur_193 () {
	static $is_inf;
	if($is_inf===NULL) {
		$is_inf = version_compare($GLOBALS['spip_version_code'],'1.9300','<');
	}
	return($is_inf);
}

/**
 * Journal de bord.
 */
function fmp3_log ($message, $flag = null, $force = true) {
	if(!empty($message) && $force) {
		$flag = 
			($flag === null)
			? ""
			: " " . (!$flag ? "ERROR" : "OK")
			;
		spip_log($message.$flag, _FMP3_PREFIX);
	}
}

/**
 * renvoie les infos du plugin contenues dans les metas
 * qui contient 'dir' et 'version'
 */
function fmp3_get_plugin_meta_infos ($prefix) {
	if(isset($GLOBALS['meta']['plugin'])) {
		$result = unserialize($GLOBALS['meta']['plugin']);
		$prefix = strtoupper($prefix);
		if(isset($result[$prefix])) {
			return($result[$prefix]);
		}
	}
	return(false);
}

/**
 * retourne le num de version du plugin lors de la dernière installation
 * présent dans les metas
 */
function fmp3_get_meta_version ($prefix) {
	$result = false;
	$info = fmp3_get_plugin_meta_infos($prefix);
	if(isset($info['version'])) {
		$result = $info['version'];
	}
	return($result);
}

/**
 * retourne le dir du plugin
 * présent dans les metas
 */
function fmp3_get_plugin_meta_dir($prefix) {
	$result = false;
	$info = fmp3_get_plugin_meta_infos($prefix);
	if(isset($info['dir'])) {
		$result = $info['dir'];
	}
	return($result);
}

/**
 * ecriture des préférences dans les metas, format sérialisé
 */
function fmp3_set_preference ($key, $value) {
	if(isset($GLOBALS['meta'][_FMP3_META_PREFERENCES])) {
		$s_meta = unserialize($GLOBALS['meta'][_FMP3_META_PREFERENCES]);
		$s_meta[$key] = $value;
		return(fmp3_set_all_preferences($s_meta));
	}
	return(false);
}

/**
 * ecriture dans les metas, format sérialisé
 * $preferences Array 
 */
function fmp3_set_all_preferences ($preferences = false) {
	$preferences =
		($preferences === false)
		? _FMP3_PREFERENCES_DEFAULT
		: serialize($preferences)
		;
	ecrire_meta(_FMP3_META_PREFERENCES, $preferences);
	return(fmp3_ecrire_metas());
}

/**
 * lecture dans les metas
 * retour: array ou false si inconnue
 */
function fmp3_get_all_preferences () {
	if(isset($GLOBALS['meta'][_FMP3_META_PREFERENCES])) {
		return(unserialize($GLOBALS['meta'][_FMP3_META_PREFERENCES]));
	}
	return(false);
}

// 
function fmp3_ecrire_metas () {
	if(fmp3_spip_est_inferieur_193()) { 
		include_spip("inc/meta");
		ecrire_metas();
	}
	return(true);
}

/**
 * dirname du fichier mp3 
 */
function fmp3_chemin_son ($objet, $id_objet) {
	$chemin = _DIR_LOGOS . $objet . $id_objet . '.mp3';
	fmp3_log("son? : ".$chemin);
	return ($chemin);
}

/**
 * Donne le contenu javascript pour afficher le bouton play
 * @author Christian Paulus
 * @param $mp3path Chemin (URL) du fichier mp3
 * @param $autoStart Démarrage auto du son (true|false)
 * @param $backColor Couleur de fond du bouton (hexa, par ex: 030303)
 * @param $frontColor Couleur du bouton (hexa, par ex: 030303)
 * @param $repeatPlay Répéter le son (boucler)
 * @param $songVolume Volume du son
 * @param $width largeur du bloc contenant le bouton
 * @param $height hauteur du bloc contenant le bouton
 * @return Code HTML à insérer dans la page
 */
function fmp3_bouton_play (
	$mp3path
	, $autoStart = "false"
	, $backColor = "030303"
	, $frontColor = "ffffff"
	, $repeatPlay = "false"
	, $songVolume = "50"
	, $width = 25
	, $height = 20
	) {
	
	// la barre de progression n'est pas utilisé par ce plug-in
	$showDownload = "false";
	
	// recherche le player
	$playerPath = url_absolue(find_in_path('swf/singlemp3player.swf'));
	
	$bouton_play = ""
		. "<!-- "._FMP3_PREFIX." -->\n"
		. "<span id=\"fmp3-sound\" class=\"mp3\">\n"
		. "<span id=\"fmp3-content\">\n"
		. "<span id=\"fmp3-object\" style=\"width:".$width."px;height:".$height."px\">"
		. "</span>\n"
		. "</span>\n"
		. "</span>\n"
		/* 
		 * Un peu de javascript pour activer le plugin jQuery fmp3
		 */
		. "
<script type=\"text/javascript\">
//<![CDATA[
$(document).ready(function(){
	$(\"#fmp3-object\").jmp3({
		playerPath: \"".$playerPath."\"
		, mp3path: \"".$mp3path."\"
		, showDownload: \"".$showDownload."\"
		, autoStart: \"".$autoStart."\"
		, backColor: \"".$backColor."\"
		, frontColor: \"".$frontColor."\"
		, repeatPlay: \"".$repeatPlay."\"
		, songVolume: \"".$songVolume."\"
		, width: ".$width."
		, height: ".$height."
	});
});
//]]>
</script>
"
	/* */
		. "<!-- / "._FMP3_PREFIX." -->\n"
		;
		
	return($bouton_play);
}

/**
 * Enveloppe le script du tag HTML
 */
function fmp3_envelopper_script ($source, $format) {
	$source = trim($source);
	if(!empty($source)) {
		switch($format) {
			case 'css':
				$source = "\n<style type='text/css'>\n<!--\n" 
					. $source
					. "\n-->\n</style>";
				break;
			case 'js':
				$source = "\n<script type='text/javascript'>\n//<![CDATA[\n" 
					. $source
					. "\n//]]>\n</script>";
				break;
			default:
				$source = "\n\n<!-- erreur envelopper: format inconnu [$format] -->\n\n";
		}
	}
	return($source);
} // end fmp3_envelopper_script()

/**
 * complément des deux 'compacte'. supprimer les espaces en trop.
 */ 
function fmp3_compacter_script ($source, $format) {
	$source = trim($source);
	if(!empty($source)) {
		$source = compacte($source, $format);
		$source = preg_replace(",/\*.*\*/,Ums","",$source); // pas de commentaires
		$source = preg_replace('=[[:space:]]+=', ' ', $source); // réduire les espaces
	}
	return($source);
} // end fmp3_compacter_script()
?>