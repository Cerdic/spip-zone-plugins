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

/*
	Les fonctions qui doivent etre chargees par tous les scripts sauf inc/spiplistes_api*
*/

// Certains hebergeurs ont desactive l'acces a syslog (free,...)
// Recreer les constantes pour trier les journaux
if(!defined("LOG_WARNING")) {
	define("LOG_WARNING", 4);
	define("LOG_DEBUG", 7);
}

function spiplistes_log($texte, $level = LOG_WARNING) {
	static $lan;
	if($lan === null) {
		$lan = spiplistes_server_rezo_local();
	}
	if($lan) {
		if(spiplistes_pref_lire('opt_console_syslog') == 'oui') {
			$tag = "_";
			if(empty($tag)) { 
				$tag = basename ($_SERVER['PHP_SELF']); 
			}
			else if($level == LOG_DEBUG) {
				$tag = "DEBUG: ".$tag; 
			}
			return(
				openlog ($tag, LOG_PID | LOG_CONS, LOG_USER) 
					&& syslog ($level, (string)$texte) 
					&&	closelog()
			);
		}
		else {
			spip_log($texte, _SPIPLISTES_PREFIX);
		}
		
	}
	else if($level < LOG_DEBUG) {
		// Taille du log SPIP trop courte en 192
		// Ne pas envoyer si DEBUG sinon tronque sans cesse
		// En SPIP 193, modifier globale $taille_des_logs pour la rotation
		spip_log($texte, _SPIPLISTES_PREFIX);
	}
	return(true);
}

function spiplistes_server_rezo_local () {
	static $lan;
	if($lan === null) {
		$lan = preg_match('/^(192\.168|127\.0)/', $_SERVER['SERVER_ADDR']);
	}
	return($lan);
}

// CP-20080324
function spiplistes_spip_est_inferieur_193 () {
	static $is_inf;
	if($is_inf===NULL) {
		$is_inf = version_compare($GLOBALS['spip_version_code'],'1.9300','<');
	}
	return($is_inf);
}

//CP-20080512
function spiplistes_pref_lire ($key) {
	return(__plugin_lire_key_in_serialized_meta($key, _SPIPLISTES_META_PREFERENCES));
}

?>