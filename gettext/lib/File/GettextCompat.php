<?php

/**
 * File::Gettext
 * Compatibility wrappers providing gettext interface.
 *
 * @category   FileFormats
 * @package    File_Gettext
 * @author     Brion Vibber <brion@pobox.com>
 * @copyright  2009 Brion Vibber
 * @license    BSD, revised
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/File_Gettext
 */

class GettextCompat {
	// Static setup for singleton...
	private static $singleton;
	
	static function singleton() {
		if (!isset(self::$singleton)) {
			self::$singleton = new GettextCompat();
		}
		return self::$singleton;
	}
	
	// Instance vars
	private $domain = 'messages';
	private $bindings = array();
	private $codesets = array();
	private $messages = array();
	
	function textdomain($domain) {
		if ($domain != '') {
			$this->domain = $domain;
		}
		return $this->domain;
	}
	
	function gettext($msgid) {
		return $this->dgettext($this->domain, $msgid);
	}
	
	function dgettext($domain_name, $msgid) {
		return $this->dcgettext($domain_name, $msgid, LC_MESSAGES);
	}
	
	function dcgettext($domain_name, $msgid, $category) {
		$lang = $this->_lang($category);
		$this->_load($domain_name, $category, $lang);
		
		if (isset($this->messages[$domain_name][$category][$lang][$msgid])) {
			return $this->messages[$domain_name][$category][$lang][$msgid];
		}
		return $msgid;
	}
	
	function bindtextdomain($domain_name, $dir) {
		// @todo does a null $dir param mean we remove an already-bound dir?
		if (empty($dir)) {
			if (isset($this->bindings[$domain_name])) {
				return $this->bindings[$domain_name];
			} else {
				// ???
				return false;
			}
		} elseif (file_exists($dir) && is_dir($dir)) {
			$this->bindings[$domain_name] = $dir;
			unset($this->messages[$domain_name]);
			return true;
		}
		return false;
	}
	
	function ngettext($msgid1, $msgid2, $count) {
		return $this->dngettext($this->domain, $msgid1, $msgid2, $count);
	}
	
	function dngettext($domain, $msgid1, $msgid2, $count) {
		return $this->dcngettext($domain, $msgid1, $msgid2, $count, LC_MESSAGES);
	}
	
	function dcngettext($domain, $msgid1, $msgid2, $count, $category) {
		if ($count == 1) {
			return $this->dcgettext($domain, $msgid1, $category);
		} else {
			return $this->dcgettext($domain, $msgid2, $category);
		}
	}
	
	function bind_textdomain_codeset($domain, $codeset) {
		if ($codeset) {
			$this->codesets[$domain] = $codeset;
		}
		if (isset($this->codesets[$domain])) {
			return $this->codesets[$domain];
		} else {
			return false;
		}
	}
	
	// Private
	private function _lang($category) {
		// Check the current locale...
		$locale = setlocale($category, "0");
		
		// This probably returned something like 'en_US.UTF-8'
		if( preg_match( '/^([a-zA-Z]+)/', $locale, $matches ) ) {
			return $matches[1];
		} else {
			return 'C';
		}
	}
	
	private function _load($domain_name, $category, $lang) {
		if (!isset($this->messages[$domain_name][$category][$lang])) {
			$subdirs = array(
				LC_MESSAGES => 'LC_MESSAGES',
				LC_ALL => 'LC_ALL',
				LC_CTYPE => 'LC_CTYPE',
				// @todo -- complete and order this list
			);
			
			if (!isset($this->bindings[$domain_name])) {
				return false;
			}
			$basedir = $this->bindings[$domain_name];
			$subdir = $subdirs[$category];
			
			$full = "$basedir/$lang/$subdir/$domain_name.mo";
			
			$messages = File_Gettext::factory('mo', $full);
			$err = $messages->load();
			if (PEAR::isError($err)) {
				throw new Exception($err->toString()); // todo... hide this?
			}
			$arr = $messages->toArray();
			$this->messages[$domain_name][$category][$lang] = $arr['strings'];
		}
		return true;
	}
}

if (!function_exists('gettext')) {
	require_once "PEAR.php";
	require_once "File/Gettext.php";
	
	function textdomain($domain) {
		return GettextCompat::singleton()->textdomain($domain);
	}
	
	function gettext($msgid) {
		return GettextCompat::singleton()->gettext($msgid);
	}
	
	function dgettext($domain_name, $msgid) {
		return GettextCompat::singleton()->dgettext($domain_name, $msgid);
	}
	
	function dcgettext($domain_name, $msgid, $category) {
		return GettextCompat::singleton()->dcgettext($domain_name, $msgid, $category);
	}
	
	function bindtextdomain($domain_name, $dir) {
		if (isset($domain_name) && isset($dir)) {
			return GettextCompat::singleton()->bindtextdomain($domain_name, $dir);
		}
		return false;
	}
	
	function ngettext($msgid1, $msgid2, $count) {
		return GettextCompat::singleton()->ngettext($msgid1, $msgid2, $count);
	}
	
	function dngettext($domain, $msgid1, $msgid2, $count) {
		return GettextCompat::singleton()->dngettext($domain, $msgid1, $msgid2, $count);
	}
	
	function dcngettext($domain, $msgid1, $msgid2, $count, $category) {
		return GettextCompat::singleton()->dcngettext($domain, $msgid1, $msgid2, $count, $category);
	}
	
	function bind_textdomain_codeset($domain, $codeset) {
		return GettextCompat::singleton()->bind_textdomain_codeset($domain, $codeset);
	}
	
	function _($msgid) {
		return gettext($msgid);
	}
	
}
