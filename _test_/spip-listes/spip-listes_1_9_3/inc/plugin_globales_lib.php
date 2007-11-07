<?php
// Original From SPIP-Listes-V :: $Id: plugin_globales_lib.php paladin@quesaco.org $
// Christian PAULUS : http://www.quesaco.org/ (c) 2007
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// librairie de fonctions et defines globales 
// indépendantes de tout plugin

// toutes les fonctions ici sont préfixées '__' (2 tirets)
// sauf re-déclaration inc/vieilles_defs.php
// sauf déclaration des fonctions php5

if (!defined("_ECRIRE_INC_VERSION")) return;

define("_PGL_PLUGIN_GLOBALES_LIB", true);

include_spip("inc/plugin");

if(!function_exists('__server_in_private_ip_adresses')) {
	// vérifie si le serveur est dans Adresses IP privées de classe C (Private IP addresses)
	// renvoie true si serveur dans classe privée
	function __server_in_private_ip_adresses() {
		return(preg_match('/^192\.168/', $_SERVER['SERVER_ADDR']));
	}
}

if(!defined("_PGL_SYSLOG_LAN_IP_ADDRESS")) define("_PGL_SYSLOG_LAN_IP_ADDRESS", "/^192\.168\./");
if(!defined("_PGL_USE_SYSLOG_UNIX"))  define("_PGL_USE_SYSLOG_UNIX", true);

// Trace sur syslog
if(!function_exists('__syslog_trace')) {
	// trace si serveur sur LAN
	if(__server_in_private_ip_adresses()) {
		function __syslog_trace($message, $priority = LOG_WARNING, $tag = "_") {
			if(empty($tag)) { 
				$tag = basename ($_SERVER['PHP_SELF']); 
			}
			else if($priority == LOG_DEBUG) {
				$tag = "DEBUG: ".$tag; 
			}
			return(
				openlog ($tag, LOG_PID | LOG_CONS, LOG_USER) 
					&& syslog ($priority, (string)$message) 
					&&	closelog()
			);
		}
	}
}

if(!function_exists('__syslog_dump')) {
	// fonction récursive
	// dump du contenu d'un var dans le log
	// pratique en mode debug
	// ne pas utiliser dans version de production
	function __syslog_dump ($p, $tag = '') {
		
		if(!__server_in_private_ip_adresses()) return false;
		
		if(is_array($p) || is_object($p)) {
			foreach($p as $k=>$v) {
				if(is_array($v) || is_object($v)) {
					__syslog_dump($v, $k);
				}
				else {
					__syslog_trace((!empty($tag) ? $tag." +> " : '')."$k => $v");
				}
			}
		}
		else {
			__syslog_trace((!empty($tag) ? $tag." +> " : '')."$p");
		}
	}
}
/* fin des __syslog_* */


/*//////////////////////////
*/
if(!function_exists('__plugin_real_version_get')) {
	// renvoie la version du fichier plugin.xml
	function __plugin_real_version_get ($prefix) {
		$r = __plugin_real_tag_get($prefix, 'version');
		return ($r);
	}
}

if(!function_exists('__plugin_real_version_base_get')) {
	// renvoie la version_base du fichier plugin.xml
	function __plugin_real_version_base_get ($prefix) {
		$r = __plugin_real_tag_get($prefix, 'version_base');
		return ($r);
	}
}

if(!function_exists('__plugin_current_version_get')) {
	function __plugin_current_version_get ($prefix) {
		return(lire_meta($prefix."_version"));
	}
}

/**/
if(!function_exists('__plugin_current_version_base_get')) {
	// renvoie la version_base en cours
		// doc: voir inc/plugin.php sur version_base (plugin.xml)
		// qui s'appelle base_version en spip_meta %-}
	function __plugin_current_version_base_get ($prefix) {
		return(lire_meta($prefix."_base_version"));
	}
} // end if __plugin_current_version_base_get

/**/
if(!function_exists('__plugin_real_tag_get')) {
	function __plugin_real_tag_get ($prefix, $s) {
		$dir = __plugin_get_meta_dir($prefix);
		$f = _DIR_PLUGINS.$dir."/"._FILE_PLUGIN_CONFIG;
		if(is_readable($f) && ($c = file_get_contents($f))) {
			$p = array("/<!--(.*?)-->/is","/<\/".$s.">.*/s","/.*<".$s.">/s");
			$r = array("","","");
			$r = preg_replace($p, $r, $c);
		}
		return(!empty($r) ? $r : false);
	}
} // end if __plugin_real_tag_get

/**/
if(!function_exists('__plugin_get_meta_infos')) {
	// renvoie les infos du plugin contenues dans les metas
	// qui contient 'dir' et 'version'
	function __plugin_get_meta_infos ($prefix) {
		if(isset($GLOBALS['meta']['plugin'])) {
			$result = unserialize($GLOBALS['meta']['plugin']);
			$prefix = strtoupper($prefix);
			if(isset($result[$prefix])) {
				return($result[$prefix]);
			}
		}
		return(false);
	}
} // end if __plugin_get_meta_infos

/**/
if(!function_exists('__plugin_get_meta_dir')) {
	// renvoie le dir du plugin
	// présent dans les metas
	function __plugin_get_meta_dir($prefix) {
		$result = false;
		$info = __plugin_get_meta_infos($prefix);
		if(isset($info['dir'])) {
			$result = $info['dir'];
		}
		return($result);
	}
} // end if __plugin_get_meta_dir

/**/
if(!function_exists('__plugin_meta_version')) {
	// renvoie le num de version du plugin lors de la dernière installation
	// présent dans les metas
	function __plugin_get_meta_version($prefix) {
		$result = false;
		$info = __plugin_get_meta_infos($prefix);
		if(isset($info['version'])) {
			$result = $info['version'];
		}
		return($result);
	}
}

/**/
if(!function_exists('__plugin_boite_meta_info')) {
	// affiche un petit bloc info sur le plugin
	function __plugin_boite_meta_info ($prefix, $return = false) {
		include_spip('inc/meta');
		$result = false;
		if(!empty($prefix)) {
			$meta_info = __plugin_get_meta_infos($prefix); // dir et version
			$info = plugin_get_infos($meta_info['dir']);
			$icon = 
				(isset($info['icon']))
				? "<div "
					. " style='width:64px;height:64px;"
						. "margin:0 auto 1em;"
						. "background: url(". _DIR_PLUGINS.$meta_info['dir']."/".trim($info['icon']).") no-repeat center center;overflow: hidden;'"
					. " title='Logotype plugin $prefix'>"
					. "</div>\n"
				: ""
				;
			//$result .= __plugin_boite_meta_info_liste($info, true); // pour DEBUG
			$result .= __plugin_boite_meta_info_liste($info, false);
			$result .= __plugin_boite_meta_info_liste($meta_info, true);
			if(!empty($result)) {
				$result = ""
					. debut_cadre_relief('plugin-24.gif', true, '', $prefix)
					. $icon
					. $result
					. fin_cadre_relief(true)
					;
			}
		}
		if($return) return($result);
		else echo($result);
	}
	/**/
	function __plugin_boite_meta_info_liste($array, $recursive = false) {
		global $spip_lang_left;
		$result = "";
		if(is_array($array)) {
			foreach($array as $key=>$value) {
				$sub_result = "";
				if(is_array($value)) {
					if($recursive) {
						$sub_result = __plugin_boite_meta_info_liste($value);
					}
				}
				else {
					$sub_result = propre($value);
				}
				if(!empty($sub_result)) {
					$result .= "<li><span style='font-weight:bold;'>$key</span> : $sub_result</li>\n";
				}
			}
			if(!empty($result)) {
				$result = "<ul style='margin:0;padding:0 1ex;list-style: none;text-align: $spip_lang_left;' class='verdana2'>$result</ul>";
			}
		}
		return($result);
	}
} // end if __plugin_boite_meta_info

/**/
if(!function_exists('__plugin_html_signature')) {
	// petite signature de plugin
	// du style "Dossier plugin [version]"
	function __plugin_html_signature ($prefix, $return = false, $html = true) {
	
		$info = plugin_get_infos(__plugin_get_meta_dir($prefix));
		$nom = typo($info['nom']);
		$version = typo($info['version']);
		//$base_version = typo($info['version_base']); // cache ?
		$base_version = __plugin_current_version_base_get($prefix);
		$revision = "";
		if($html) {
			$version = (($version) ? " <span style='color:gray;'>".$version."</span>" : "");
			$base_version = (($base_version) ? " <span style='color:#66c;'>&lt;".$base_version."&gt;</span>" : "");
		}
		$result = ""
			. $nom
			. $version
			. $base_version
			;
		if($html) {
			$result = "<p class='verdana1 spip_xx-small' style='font-weight:bold;'>$result</p>\n";
		}
		if($return) return($result);
		else echo($result);
	}
} // end if __plugin_html_signature

/*//////////////////////////
*/

if(!function_exists('__mysql_date_time')) {
	function __mysql_date_time($time = 0) {
		return(date("Y-m-d H:i:s", $time));
	}
}

if(!function_exists('__is_mysql_date')) {
	function __is_mysql_date($date) {
		$t = strtotime($date);
		return($t && ($date == __mysql_date_time($t)));
	}
}

if(!function_exists('__mysql_max_allowed_packet')) {
	// renvoie la taille maxi d'un paquet PySQL (taille d'une requête)
	function __mysql_max_allowed_packet() {
		$sql_result = spip_query("SHOW VARIABLES LIKE 'max_allowed_packet'");
		$row = spip_fetch_array($sql_result, SPIP_NUM);
		return($row[1]);
	}
}

// si inc/vieilles_defs.php disparaît ?
if(!function_exists("lire_meta")) {
	function lire_meta($key) {
		$result = 0;
		if(_FILE_CONNECT && @file_exists(_FILE_CONNECT_INS .'.php')) {
			if(!isset($GLOBALS['meta'][$key])) {
				$sql_result = @spip_query("SELECT valeur FROM spip_meta WHERE nom="._q($key)." LIMIT 1");
				if($row = spip_fetch_array($sql_result)) {
					$result = $row[$key];
					$GLOBALS['meta'][$key] = $result;
				}
			}
			else {
				$result = $GLOBALS['meta'][$key];
			}
		}
		return($result);
	}
}

if(!function_exists("__boite_alerte")) {
	// Renvoie ou affiche une boite d'alerte
	function __boite_alerte ($message, $return = false) {
		$result = ""
			. debut_cadre_enfonce('', true)
			.  http_img_pack("warning.gif", _T('info_avertissement'), 
					 "style='width: 48px; height: 48px; float: right;margin: 5px;'")
			. "<strong>$message</strong>\n"
			. fin_cadre_enfonce(true)
			. "\n<br />"
			;
		if($return) return($result);
		else echo($result);
	}
}

// !(PHP 4 >= 4.3.0, PHP 5)
if(!function_exists("html_entity_decode")) {
	function html_entity_decode ($string, $quote_style = "", $charset = "")
	{
		// Remplace les entités numériques
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
		// Remplace les entités litérales
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr ($string, $trans_tbl);
	}
}

if((phpversion()<5) && !function_exists("__html_entity_decode_utf8")) {
// http://fr.php.net/html_entity_decode
	// thank to: laurynas dot butkus at gmail dot com
	function __html_entity_decode_utf8($string)
	{
		 static $trans_tbl;
		
		 // replace numeric entities
		 $string = preg_replace('~&#x([0-9a-f]+);~ei', '__code2utf(hexdec("\\1"))', $string);
		 $string = preg_replace('~&#([0-9]+);~e', '__code2utf(\\1)', $string);
	
		 // replace literal entities
		 if (!isset($trans_tbl))
		 {
			  $trans_tbl = array();
			 
			  foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key)
					$trans_tbl[$key] = utf8_encode($val);
		 }
		
		 return strtr($string, $trans_tbl);
	} // __html_entity_decode_utf8()
	
	// Returns the utf string corresponding to the unicode value (from php.net, courtesy - romans@void.lv)
	// thank to: akniep at rayo dot info
	function __code2utf($number)  {
        if ($number < 0)
            return FALSE;
        if ($number < 128)
            return chr($number);
        // Removing / Replacing Windows Illegals Characters
        if ($number < 160)
        {
                if ($number==128) $number=8364;
            elseif ($number==129) $number=160; // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            elseif ($number==130) $number=8218;
            elseif ($number==131) $number=402;
            elseif ($number==132) $number=8222;
            elseif ($number==133) $number=8230;
            elseif ($number==134) $number=8224;
            elseif ($number==135) $number=8225;
            elseif ($number==136) $number=710;
            elseif ($number==137) $number=8240;
            elseif ($number==138) $number=352;
            elseif ($number==139) $number=8249;
            elseif ($number==140) $number=338;
            elseif ($number==141) $number=160; // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            elseif ($number==142) $number=381;
            elseif ($number==143) $number=160; // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            elseif ($number==144) $number=160; // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            elseif ($number==145) $number=8216;
            elseif ($number==146) $number=8217;
            elseif ($number==147) $number=8220;
            elseif ($number==148) $number=8221;
            elseif ($number==149) $number=8226;
            elseif ($number==150) $number=8211;
            elseif ($number==151) $number=8212;
            elseif ($number==152) $number=732;
            elseif ($number==153) $number=8482;
            elseif ($number==154) $number=353;
            elseif ($number==155) $number=8250;
            elseif ($number==156) $number=339;
            elseif ($number==157) $number=160; // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            elseif ($number==158) $number=382;
            elseif ($number==159) $number=376;
        } //if
       
        if ($number < 2048)
            return chr(($number >> 6) + 192) . chr(($number & 63) + 128);
        if ($number < 65536)
            return chr(($number >> 12) + 224) . chr((($number >> 6) & 63) + 128) . chr(($number & 63) + 128);
        if ($number < 2097152)
            return chr(($number >> 18) + 240) . chr((($number >> 12) & 63) + 128) . chr((($number >> 6) & 63) + 128) . chr(($number & 63) + 128);
       
        return FALSE;
    } //__code2utf()
}

if(!function_exists('__texte_html_2_iso')) {
	function __texte_html_2_iso($string, $charset, $unspace = false) {
		$charset = strtoupper($charset);
		// html_entity_decode a qq soucis avec UTF-8
		if($charset=="UTF-8" && (phpversion()<5)) {
			$string = __html_entity_decode_utf8($string);
		}
		else {
			$string = html_entity_decode($string, ENT_QUOTES, $charset);
		}
		if($unspace) {
			// renvoie sans \r ou \n pour les boites d'alerte javascript
			$string = preg_replace("/([[:space:]])+/", " ", $string);
		}
		return ($string);
	}
}

if(!function_exists('__boite_select_de_formulaire')) {
	// renvoie une boite select pour un formulaire
	function __boite_select_de_formulaire ($array_values, $selected, $select_id, $select_name
		, $size=1, $select_style='', $select_class=''
		, $label_value='', $label_style='', $label_class='', $multiple=false
		) {
		$result = "";
		foreach($array_values as $key=>$value) {
			$result .= "<option".mySel($value, $selected).">$key</option>\n";
		}
		$result = ""
			. (
				(!empty($label_value))
				? "<label for='$select_id'"
					.(!empty($label_style) ? " style='$label_style'" : "")
					.(!empty($label_class) ? " class='$label_class'" : "")
					.">$label_value</label>\n" 
				: ""
				)
			. "<select name='$select_name' size='$size'"
				.(($multiple && ($size>1)) ? " multiple='multiple'" : "")
				.(!empty($select_style) ? " style='$select_style'" : "")
				.(!empty($select_class) ? " class='$select_class'" : "")
				." id='$select_id'>\n"
			. $result
			. "</select>\n"
			;
		return($result);
	} // __boite_select_de_formulaire()
}

if(!function_exists('__array_values_in_keys')) {
	// renvoie tableau avec key => value 
	// (pratique pour __boite_select_de_formulaire())
	function __array_values_in_keys($array) {
		$result = array();
		foreach($array as $value) {
			$result[$value] = $value;
		}
		return($result);
	} // __array_values_in_keys()
}

if(!function_exists('__plugin_aide')) {
	// petit bouton aide à placer à droite du titre de bloc
	function __plugin_aide ($fichier_exec_aide, $aide='', $return=true) {
		include_spip('inc/minipres');
		global $spip_lang
			, $spip_lang_rtl
			, $spip_display
			;
		if (!$aide || $spip_display == 4) return;
		
		$t = _T('titre_image_aide');
		$result = ""
		. "\n&nbsp;&nbsp;<a class='aide'\nhref='"
		. generer_url_ecrire($fichier_exec_aide, "var_lang=$spip_lang")
		. (
			(!empty($aide)) 
			? "#$aide" 
			: ""
			)
		. "'"
		. " onclick=\"javascript:window.open(this.href,'spip_aide', 'scrollbars=yes, resizable=yes, width=740, height=580'); return false;\">\n"
		. http_img_pack(
			"aide".aide_lang_dir($spip_lang,$spip_lang_rtl).".gif"
			, _T('info_image_aide')
			, " title=\"$t\" class='aide'"
			)
		. "</a>"
		;
		
		if($return) return($result);
		else echo($result);
	} // __plugin_aide()
}

if(!function_exists('__plugin_ecrire_key_in_serialized_meta')) {
	// ecriture dans les metas, format sérialisé
	function __plugin_ecrire_key_in_serialized_meta ($key, $value, $meta_name) {
		$s_meta = unserialize($GLOBALS['meta'][$meta_name]);
		$s_meta[$key] = $value;
		ecrire_meta($meta_name, serialize($s_meta));
		return(true);
	}
}

if(!function_exists('__plugin_lire_serialized_meta')) {
	// lecture dans les metas, format sérialisé
	function __plugin_lire_serialized_meta ($meta_name) {
		if(isset($GLOBALS['meta'][$meta_name])) {
			return(unserialize($GLOBALS['meta'][$meta_name]));
		}
		return(false);
	}
}

if(!function_exists('__plugin_lire_key_in_serialized_meta')) {
// lecture d'une clé dans la meta sérialisée
	function __plugin_lire_key_in_serialized_meta ($key, $meta_name) {
		$result = false;
		$s_meta = __plugin_lire_serialized_meta($meta_name);
		if($s_meta) {
spip_log("???? $key ");
}
		if($s_meta && isset($s_meta[$key])) {
			$result = $s_meta[$key];
spip_log("___ $key: ".$s_meta[$key]);
		}
		return($result);
	}
}

if(!function_exists('__table_items_count')) {
	function __table_items_count ($table, $key, $where='') {
		return (
			(($row = spip_fetch_array(spip_query("SELECT COUNT($key) AS n FROM $table $where"))) && $row['n'])
			? intval($row['n'])
			: 0
		);
	}
}

if(!function_exists('__table_items_get')) {
	function __table_items_get ($table, $keys, $where='', $limit='') {
		$result = array();
		$sql_result = spip_query("SELECT $keys FROM $table $where $limit");
		while($row = spip_fetch_array($sql_result)) { $result[] = $row; }
		return ($result);
	}
}

if(!function_exists('__ecrire_metas')) {
	function __ecrire_metas () {
		if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) { 
			include_spip("inc/meta");
			ecrire_metas();
		}
		return(true);
	}
}

if(!function_exists("array_fill_keys")) {
	function array_fill_keys($array, $fill) {
		$result = array();
		foreach($array as $key) {
				$result[$key] = $fill;
		}
		return ($result);
	}
} 

?>