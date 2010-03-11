<?php
/*
 **********************************************
 * Copyright (c) 2009 Christian Paulus - http://www.quesaco.org
 * Dual licensed under the MIT and GPL licenses.
 **********************************************
 */
// $LastChangedBy$
// $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

define('_FONI_PREFIX', 'foni');
define('_FONI_META_PREFERENCES', 'foni_preferences');
define('_FONI_FONTS_DIR', _DIR_PLUGIN_FONI . 'polices/');
define('_FONI_IMAGES_DIR', _DIR_PLUGIN_FONI . 'images/');
define('_FONI_SEPARATOR', '|');
define('_FONI_DEBUG', false); //true);

define('_FONI_DEFAULTS_PREFS'
	, serialize(array(
		
		 // liste des fontes sélectionnées
		'fontes' => array()
		
		// importer la fonte dans la page
		// si 'non', créer un lien classique
		, 'include' => 'oui'
	))
);

/**
 * Journal (prive_spip.log si espace prive, sinon spip.log)
 * @return boolean
 * @param string $msg
 */
function foni_log ($msg) {
	static $prev, $tag;
	static $count = 0;
	static $tag;
	
	if(!$tag) {
		$tag = '[' . _FONI_PREFIX . '] ';
	}
	
	$msg = trim($msg);
	if($prev != $msg) {
		if($count) {
			spip_log($tag . '--- last message repeated '.$count.' times ---');
			$count = 0;
		}
		$prev = $msg;
		spip_log($tag . $msg);
	}
	else {
		$count++;
	}
	return(true);
}

/**
 * Les preferences par defaut
 * @return array
 */
function foni_preferences_defaut () 
{
	static $defaut;

	if($defaut === null) {
		$defaut = unserialize(_FONI_DEFAULTS_PREFS);
	}
	return($defaut);
}

/*
 * Lire les preferences enregistrees dans la table spip_meta
 * @return array les preferences
 * @param $forcer bool[optional] true pour forcer la lecture dans la base
 */
function foni_lire_preferences ($forcer = false) 
{
	static $prefs;

	if($forcer || ($prefs === null)) 
	{
		$enregistrer = false;
		
		$prefs = ($p = $GLOBALS['meta'][_FONI_META_PREFERENCES]) ? unserialize($p) : array();
		
		$prefs_defaut = foni_preferences_defaut();
		
		foreach($prefs_defaut as $key => $value) {
			foni_log("$key $value");
			if(!isset($prefs[$key])) {
				$prefs[$key] = $value;
				$enregistrer = true;
			}
		}
		if($enregistrer) {
			$prefs = foni_ecrire_preferences ($prefs);
		}
	}
	return ($prefs);
}

/*
 * Ecrire les preferences dans la table spip_meta
 * @return array
 * @param $cur_prefs array
 */
function foni_ecrire_preferences ($prefs) 
{
	$s = serialize($prefs);
	foni_log($s);
	ecrire_meta(_FONI_META_PREFERENCES, $s);
	
	return($prefs);
}

function foni_font_encode ($font_path, $font_file)
{
	if(($ii = strlen($font_file)) && ($ii > 4))
	{
		$ext = strtolower(substr($font_file, -3));
	}
	switch($ext) {
		case 'eot':
			$mime = 'application/vnd.ms-fontobject';
			break;
		case 'otf':
		case 'ttf':
			$mime = 'application/octet-stream';
			break;
		default:
			$mime = 'erreur type mime : '.$ext;
	}
	
	$file = (test_espace_prive() ? '../' : '') . $font_path . '/' . $font_file;
	$base64 =
		($contents = file_get_contents($file, FILE_BINARY))
		? base64_encode($contents)
		: 'font file illisible: ' .$file
		;
		
	return ('data:' . $mime . ';base64,' . $base64);
}

function balise_FONI_FONT_ENCODE ($p) {

	if(
	   ($font_path = trim(interprete_argument_balise(1, $p)))
	   && ($font_file = trim(interprete_argument_balise(2, $p)))
	)
	{
		foni_log('recu balise: '.$font_file);
		$p->code = "foni_font_encode ($font_path, $font_file)";
	}
		
	return($p);
}

function foni_header_sig ()
{
	return(PHP_EOL . '<!-- ' . _FONI_PREFIX . ' -->' . PHP_EOL . $result . PHP_EOL);
}

if(!function_exists('import_script'))
{
	/**
	 * Retourne une version compactée de code js ou css,
	 * destiné à être inséré dans le code HTML
	 * @author cpaulus
	 * @version 2009122401
	 * @param string $filename
	 * @return string
	 */
	function import_script ($filename)
	{
		if($code = file_get_contents($filename))
		{
			$replace = array( // supprimer
				'@/\*.*?\*/@s' => '' // commentaires longs
				, '@//.*$@m' => '' // commentaires courts
				, '@\t@' => ' ' // tabulations
				, '@ +@' => ' ' // espaces en trop
				, '@^ +@m' => '' // espace début ligne en trop
				, '@[\n ]*([{}+=:;,])[ ]*@' => "$1" // espaces inutiles
				, '@[\r\n]+@' => "\n" // les sauts de ligne en trop
				, "@([{};])\n@" => "$1" // les retours inutiles
				);
	  		$code = preg_replace(array_keys($replace), $replace, $code);
		}
		return(trim($code));
	}
}
