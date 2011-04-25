<?php
/**
 * Les fonctions qui doivent etre chargees par tous les scripts sauf inc/spiplistes_api*
 * 
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

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

if(!defined('_ECRIRE_INC_VERSION')) return;

// Certains hebergeurs ont desactive l'acces a syslog (free,...)
// Recreer les constantes pour trier les journaux
if(!defined('LOG_WARNING')) {
	define('LOG_WARNING', 4);
	define('LOG_DEBUG', 7);
}

function spiplistes_log ($texte, $level = LOG_WARNING) {
	
	static $lan, $syslog, $debug;
	
	$texte = trim ($texte);
	
	if (empty ($texte)) { return (false); }
	
	if ($syslog === null)
	{
		$lan = spiplistes_server_rezo_local();
		
		$syslog = (spiplistes_pref_lire('opt_console_syslog') == 'oui');
		$debug = (spiplistes_pref_lire('opt_console_debug') == 'oui');
	}
	if ($debug || $lan)
	{
		if ($syslog)
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
					&& syslog ($level, $texte) 
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

/**
 * CP-20110311
 * Envoyer un message dans la console (debug)
 * Le mode debug doit etre selectionne' dans la page de configuration
 * @return bool
 */
function spiplistes_debug_log ($msg = '')
{
	static $debug;
		
	if ($debug === null)
	{
		$debug = (spiplistes_pref_lire('opt_console_debug') == 'oui');
	}
	$msg = trim ($msg);
	if ($debug && !empty($msg))
	{
		spiplistes_log ($msg, LOG_DEBUG);
	}
	
	return ($debug);
}

/**
 * CP-20110322
 * alias pour récupérer le mode debug
 * @return bool
 */
function spiplistes_debug_mode () {
	return (spiplistes_debug_log () );
}

/**
 * CP-20110311
 * Détecter si reseau local
 * @return boolean
 */
function spiplistes_server_rezo_local () {
	
	static $islan;
	
	if ($islan === null)
	{
		$adr = $_SERVER['SERVER_ADDR'];
		
		$islan =
			($adr && (
					  $adr == '127.0.0.1'
				   || (substr ($adr, 0, 8) == '192.168.')
				   || (substr ($adr, 0, 4) == '172.')
				   || (substr ($adr, 0, 3) == '10.')
				   )
			);
	}
	return ($islan);
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
		else spiplistes_debug_log ("erreur sur meta $meta_name (vide)");
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
	include_spip('inc/plugin');
	$dir = spiplistes_get_meta_dir($prefix);
	$f = _DIR_PLUGINS.$dir.'/'._FILE_PLUGIN_CONFIG;
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

function spiplistes_sqlerror_log ($trace = '') {
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
			spiplistes_log('ERROR: sender email address missing');
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

// !(PHP 4 >= 4.3.0, PHP 5)
if(!function_exists('html_entity_decode')) {
	function html_entity_decode ($string, $quote_style = '', $charset = '')
	{
		// Remplace les entites numeriques
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
		// Remplace les entites 
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr ($string, $trans_tbl);
	}
}

/**
 * complete caracteres manquants dans HTML -> ISO
 * @return la chaine transcrite
 * @param $texte le texte a transcrire
 * @param $charset le charset souhaite'. Normalement 'iso-8859-1' (voir page de config)
 * @param $is_html flag. Pour ne pas transcrire completement la version html
 * @see http://fr.wikipedia.org/wiki/ISO_8859-1
 * @see http://www.w3.org/TR/html401/sgml/entities.html
 */
function spiplistes_translate_2_charset ($texte, $charset='AUTO', $is_html = false)
{
	$texte = charset2unicode($texte);
	
	$texte = unicode2charset($texte, $charset);
	
	if ($is_html) {
		$texte = spiplistes_html_entity_decode ($texte, $charset);
	}
	if($charset != 'utf-8') {
		$texte = spiplistes_iso2ascii ($texte, $is_html);		
	}
	return($texte);
}

function spiplistes_iso2ascii ($texte, $is_html = false) {
	$remplacements = array(
		'&#8217;' => "'"	// quote
		, '&#8220;' => '"' // guillemets
		, '&#8221;' => '"' // guillemets
		)
		;
	if(!$is_html) {
		$remplacements = array_merge(
			$remplacements
			, array(
						// Latin Extended
				  '&#255;' => chr(255) // 'ÿ' // yuml inconnu php ?
				, '&#338;' => 'OE'  // OElig
				, '&#339;' => 'oe'  // oelig
				, '&#352;' => 'S'  // Scaron
				, '&#353;' => 's'  // scaron
				, '&#376;' => 'Y'  // Yuml
					// General Punctuation
				, '&#8194;' => ' ' // ensp
				, '&#8195;' => ' ' // emsp
				, '&#8201;' => ' ' // thinsp
				, '&#8204;' => ' ' // zwnj
				, '&#8205;' => ' ' // zwj
				, '&#8206;' => ' ' // lrm
				, '&#8207;' => ' ' // rlm
				, '&#8211;' => '-' // ndash
				, '&#8212;' => '--' // mdash
				, '&#39;' => "'" // apos
				, '&#8216;' => "'" // lsquo
				, '&#8217;' => "'" // rsquo
				, '&#8218;' => "'" // sbquo
				, '&#8220;' => '"' // ldquo
				, '&#8221;' => '"' // rdquo
				, '&#8222;' => '"' // bdquo
				, '&#8224;' => '+' // dagger
				, '&#8225;' => '++' // Dagger
				, '&#8240;' => '0/00' // permil
				, '&#8249;' => '.' // lsaquo
				, '&#8250;' => '.' // rsaquo
					// sans oublier
				, '&#8364;' => 'euros'  // euro
			)
		);
	}
	$texte = strtr($texte, $remplacements);
	
	return ($texte);
}

/**
 * Extension de html_entity_decode()
 * pour transposer les entites HTML étendues (UTF)
 * @return string
 */
function spiplistes_html_entity_decode ($texte, $charset = _SPIPLISTES_CHARSET_ENVOI)
{
	$charset = strtoupper ($charset);
	$texte = html_entity_decode ($texte, ENT_QUOTES, $charset);
	return ($texte);
}

// http://fr.php.net/html_entity_decode
	// thank to: laurynas dot butkus at gmail dot com
function spiplistes_html_entity_decode_utf8 ($string)
{
	 static $trans_tbl;
	
	 // replace numeric entities
	 $string = preg_replace('~&#x([0-9a-f]+);~ei', 'spiplistes_code2utf(hexdec("\\1"))', $string);
	 $string = preg_replace('~&#([0-9]+);~e', 'spiplistes_code2utf(\\1)', $string);

	 // replace literal entities
	 if (!isset($trans_tbl))
	 {
		  $trans_tbl = array();
		 
		  foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key)
				$trans_tbl[$key] = utf8_encode($val);
	 }
	
	 return strtr($string, $trans_tbl);
} // spiplistes_html_entity_decode_utf8()


// Returns the utf string corresponding to the unicode value (from php.net, courtesy - romans@void.lv)
// thank to: akniep at rayo dot info
function spiplistes_code2utf($number)  {
	static $windows_illegals_chars;
	if($windows_illegals_chars === null) {
		$windows_illegals_chars = array(
			128 => 8364
            , 129 => 160 // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            , 130 => 8218
            , 131 => 402
            , 132 => 8222
            , 133 => 8230
            , 134 => 8224
            , 135 => 8225
            , 136 => 710
            , 137 => 8240
            , 138 => 352
            , 139 => 8249
            , 140 => 338
            , 141 => 160 // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            , 142 => 381
            , 143 => 160 // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            , 144 => 160 // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            , 145 => 8216
            , 146 => 8217
            , 147 => 8220
            , 148 => 8221
            , 149 => 8226
            , 150 => 8211
            , 151 => 8212
            , 152 => 732
            , 153 => 8482
            , 154 => 353
            , 155 => 8250
            , 156 => 339
            , 157 => 160 // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            , 158 => 382
            , 159 => 376
		);
	}
	
    if ($number < 0)
        return FALSE;
    if ($number < 128)
        return chr($number);
    // Removing / Replacing Windows Illegals Characters
    if ($number < 160) {
    	$number = $windows_illegals_chars[$number];
    }
   
    if ($number < 2048)
        return chr(($number >> 6) + 192) . chr(($number & 63) + 128);
    if ($number < 65536)
        return chr(($number >> 12) + 224) . chr((($number >> 6) & 63) + 128) . chr(($number & 63) + 128);
    if ($number < 2097152)
        return chr(($number >> 18) + 240) . chr((($number >> 12) & 63) + 128) . chr((($number >> 6) & 63) + 128) . chr(($number & 63) + 128);
   
    return (false);
} //spiplistes_code2utf()

/**
 * CP-20110320
 * Version liens_absolus compatible DATA URL SHEME
 * @return string
 */
function spiplistes_liens_absolus ($texte, $base='') {
	
	static $url_sheme = 'data:image/png;base64';
	static $hide_sheme = '<__HIDEME__ ';
	$switch_me = false;
	
	if (preg_match_all(
		// masque = tout
		',(?P<masque>'
		// tag, 'img' uniquement
		. '<(?P<tag>img)[[:space:]]+[^<>]*'
		// src ? data url sheme ?
		.'(?P<attr>src=["\']?'.$url_sheme.')'
		// tout ce qui suit jusqu'au tag fermant
		. '(?P<right>[^>]*>)),isS', 
		$texte, $matches, PREG_SET_ORDER
	)) {
		foreach ($matches as $match) {
			
			$texte = str_replace(
				$match['masque']
				, $hide_sheme.$match['right']
				, $texte
				);
		}
		$switch_me = true;
	}
	
	$texte = liens_absolus($texte, $base);
	
	if ($switch_me)
	{
		$texte = str_replace($hide_sheme, '<img src="'.$url_sheme, $texte);
	}
	return ($texte);
}

