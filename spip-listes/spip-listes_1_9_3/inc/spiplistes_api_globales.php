<?php

// inc/spiplistes_api_globales.php

/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if(!defined('_ECRIRE_INC_VERSION')) return;

/*
	Les fonctions qui doivent etre chargees par tous les scripts sauf inc/spiplistes_api*
*/

// Certains hebergeurs ont desactive l'acces a syslog (free,...)
// Recreer les constantes pour trier les journaux
if(!defined("LOG_WARNING")) {
	define("LOG_WARNING", 4);
	define("LOG_DEBUG", 7);
}

function spiplistes_log ($texte, $level = LOG_WARNING) {
	
	static $lan, $syslog, $debug;
	
	if ($lan === null) {
		$lan = spiplistes_server_rezo_local();
		$syslog = (spiplistes_pref_lire('opt_console_syslog') == 'oui');
		$debug = (spiplistes_pref_lire('opt_console_debug') == 'oui');
	}
	if ($debug || $lan)
	{
		if($syslog)
		{
			$tag = '_';
			if (empty($tag))
			{ 
				$tag = basename ($_SERVER['PHP_SELF']); 
			}
			else if ($level == LOG_DEBUG)
			{
				$tag = 'DEBUG: ' . $tag; 
			}
			return (
				openlog ($tag, LOG_PID | LOG_CONS, LOG_USER) 
					&& syslog ($level, (string)$texte) 
					&&	closelog()
			);
		}
		else {
			spip_log ($texte, _SPIPLISTES_PREFIX);
		}
		
	}
	else if($level <= LOG_WARNING)
	{
		// Taille du log SPIP trop courte en 192
		// Ne pas envoyer si DEBUG sinon tronque sans cesse
		// En SPIP 193, modifier globale $taille_des_logs pour la rotation
		spip_log ($texte, _SPIPLISTES_PREFIX);
	}
	return (true);
}

function spiplistes_server_rezo_local () {
	
	static $islan;
	
	if($islan === null) {
		$adr = $_SERVER['SERVER_ADDR'];
		$islan =
			($adr && (
					  (strpos($adr, '192.168') !== false)
				   || (strpos($adr, '127.0') !== false)
				   )
			);
	}
	return($islan);
}

// CP-20080324
// SPIP 1.9.2e: $spip_version_branche = null; $spip_version_affichee = '1.9.2e'; $spip_version_code = 1.9208;
// SPIP 1.9.2f: $spip_version_branche = null; $spip_version_affichee = '1.9.2f'; $spip_version_code = 1.9208;
// SPIP 1.9.2g: $spip_version_branche = null; $spip_version_affichee = '1.9.2g'; $spip_version_code = 1.9208;
// SPIP 2.0.0: $spip_version_branche = "2.0.0"; $spip_version_affichee = "$spip_version_branche"; $spip_version_code = 12691;
// SPIP 2.0.1: $spip_version_branche = "2.0.1"; $spip_version_affichee = "$spip_version_branche"; $spip_version_code = 12691;
// SPIP 2.0.2: $spip_version_branche = "2.0.2"; $spip_version_affichee = "$spip_version_branche"; $spip_version_code = 12691;
function spiplistes_spip_est_inferieur_193 () {
	static $is_inf;
	if($is_inf===NULL) {
		$is_inf = version_compare($GLOBALS['spip_version_code'],'1.9300','<');
	}
	return($is_inf);
}


/**
 * ecrire dans la table 'spip_meta' le champ...
 * en general pour les preferences
 * @return true
 */
function spiplistes_ecrire_metas() {
	if(spiplistes_spip_est_inferieur_193()) { 
		include_spip('inc/meta');
		ecrire_metas();
	}
	return (true);
}

/**
 * Lecture d'une pref (meta)
 * @param $key string
 * @return string or null
 */
function spiplistes_pref_lire ($key) {
	$s = spiplistes_lire_key_in_serialized_meta($key, _SPIPLISTES_META_PREFERENCES);
	return ($s);
}

/*
 * lecture dans les metas, format serialise
 * @return 
 * @param $meta_name Object
 */
function spiplistes_lire_serialized_meta ($meta_name) {
	if(isset($GLOBALS['meta'][$meta_name])) {
		if(!empty($GLOBALS['meta'][$meta_name])) {
			return(unserialize($GLOBALS['meta'][$meta_name]));
		}
		else spiplistes_log("erreur sur meta $meta_name (vide)", _SPIPLISTES_LOG_DEBUG);
	}
	return(false);
}

/*
 * lecture d'une cle dans la meta serialisee
 * @return 
 * @param $key Object
 * @param $meta_name Object
 */
function spiplistes_lire_key_in_serialized_meta ($key, $meta_name) {
	$result = false;
	$s_meta = spiplistes_lire_serialized_meta($meta_name);
	if($s_meta && isset($s_meta[$key])) {
		$result = $s_meta[$key];
	} 
	return($result);
}

/*
 * ecriture dans les metas, format serialise
 * @return 
 * @param $key la cle meta a appliquer
 * @param $value sa valeur
 * @param $meta_name nom du champ meta
 */
function spiplistes_ecrire_key_in_serialized_meta ($key, $value, $meta_name) {
	if(isset($GLOBALS['meta'][$meta_name])) {
		$s_meta = unserialize($GLOBALS['meta'][$meta_name]);
		$s_meta[$key] = $value;
		ecrire_meta($meta_name, serialize($s_meta));
		return(true);
	}
	else return(false);
}


/*
 * @return la version du fichier plugin.xml 
 */
function spiplistes_real_version_get ($prefix) {
	static $r;
	if($r === null) {
		$r = spiplistes_real_tag_get($prefix, 'version');
	}
	return ($r);
}

/*
 * renvoie la version_base du fichier plugin.xml
 */
function spiplistes_real_version_base_get ($prefix) {
	$r = spiplistes_real_tag_get($prefix, 'version_base');
	return ($r);
}

function spiplistes_current_version_get ($prefix) {
	global $meta; 
	return $meta[$prefix."_version"];
}

function spiplistes_real_tag_get ($prefix, $s) {
	include_spip("inc/plugin");
	$dir = spiplistes_get_meta_dir($prefix);
	$f = _DIR_PLUGINS.$dir."/"._FILE_PLUGIN_CONFIG;
	if(is_readable($f) && ($c = file_get_contents($f))) {
		$p = array("/<!--(.*?)-->/is","/<\/".$s.">.*/s","/.*<".$s.">/s");
		$r = array("","","");
		$r = preg_replace($p, $r, $c);
	}
	return(!empty($r) ? $r : false);
}

/*
 * renvoie les infos du plugin contenues dans les metas
 * qui contient 'dir' et 'version'
 */
function spiplistes_get_meta_infos ($prefix) {
	if(isset($GLOBALS['meta']['plugin'])) {
		$result = unserialize($GLOBALS['meta']['plugin']);
		$prefix = strtoupper($prefix);
		if(isset($result[$prefix])) {
			return($result[$prefix]);
		}
	}
	return(false);
}

/*
 * renvoie le dir du plugin present dans les metas
 */
function spiplistes_get_meta_dir($prefix) {
	$result = false;
	$info = spiplistes_get_meta_infos($prefix);
	if(isset($info['dir'])) {
		$result = $info['dir'];
	}
	return($result);
}

/*
 * @return la version_base en cours
 * doc: voir inc/plugin.php sur version_base (plugin.xml)
 * qui s'appelle base_version en spip_meta %-}
 */
function spiplistes_current_version_base_get ($prefix) {
	global $meta;
	if(!($vb = $meta[$prefix."_base_version"])) {
		$vb = spiplistes_real_version_base_get ($prefix);
	}
	return($vb);
}

function spiplistes_sqlerror_log ($trace = "") {
	if($trace) $trace = " ($trace) ";
	spiplistes_log('DB ERROR'.$trace.": [" . sql_errno() . "] " . sql_error());
	return(true);
}

// CP-20090111. log pour les apis
function spiplistes_log_api ($msg) {
	static $ii;
	if($ii === null) {
		$ii = $GLOBALS['auteur_session']['id_auteur'];
		$ii = $ii ? "id_auteur #".$ii : "himself";
	};
	spiplistes_log("API: $msg by $ii");
	return(true);
}

// CP-20090111: adresse mail de l'expediteur par defaut
function spiplistes_email_from_default () {
	static $default;
	if(!$default) {
		if(
			// prendre d'abord celui par defaut de SPIP-Listes
			($result = email_valide($ii = trim($GLOBALS['meta']['email_defaut'])))
			// sinon celui du webmaster
			|| ($result = email_valide($ii = trim($GLOBALS['meta']['email_webmaster'])))
		) {
			if($result == $ii) {
				//$nom = extraire_multi($GLOBALS['meta']['nom_site']);
				//$nom = unicode2charset(charset2unicode($nom),$GLOBALS['meta']['spiplistes_charset_envoi']);
				//$result = "\"$nom\" <$ii>";
			}
		}
		else {
			spiplistes_log("ERROR: sender email address missing");
		}
	}
	return($result);
}

// PHP 4 ?
if(!function_exists('array_combine')) {
	function array_combine ($keys, $values) {
		if(is_array($keys) && is_array($values) && (count($keys) == count($values))) {
			$keys = array_values($keys);
			$values = array_values($values);
			$result = array();
			foreach($keys as $key => $value) {
				$result[$value] = $values[$key];
			}
		}
		return($result);
	}
}

?>