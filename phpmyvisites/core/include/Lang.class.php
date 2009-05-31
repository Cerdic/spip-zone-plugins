<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: Lang.class.php,v 1.13 2005/12/22 20:43:57 matthieu_ Exp $



require_once INCLUDE_PATH . "/core/include/Cookie.class.php";
require_once INCLUDE_PATH . "/core/include/Logs.functions.php";

class Lang
{
	// content of the config file
	var $langAvailable;
	
	// lang file 
	var $file;
	
	// config file
	var $fileAdress;
	
	function Lang( )
	{
		//$c = new Cookie(COOKIE_NAME_VIEW);
		
		// look if reload lang file
		$this->fileAdress = _PHPMV_DIR_CONFIG . "/lang_available.php";
		if(!file_exists( $this->fileAdress ))
		{
			$this->reloadLangFile();
		}
		else
		{
			require $this->fileAdress;
			$this->langAvailable = $langAvailable;
		}
		
		/*$langRequest = Request::getLang();
		if(!file_exists( LANGS_PATH . "/". $langRequest))
		{
			// cookie ?
			if(($langRequest = $c->getVar('lang'))
				&& file_exists(LANGS_PATH . "/" . $langRequest))
			{
				$this->file = $langRequest;
			}
		}
		else
		{
			$this->file = $langRequest;
		}*/
		$langRequest = $GLOBALS['spip_lang']."-utf-8.php";
		if(file_exists( LANGS_PATH . "/". $langRequest))
		{
			$this->file = $langRequest;
		}
		
		$lang = array();
		
		// set array global
		if( !isset($this->file)
			|| !strpos( $this->file, 'utf-8.php')
			|| strpos( $this->file, '..')
			)
		{
			$this->file = $this->getNearestLang();
		}
		require LANGS_PATH . "/" . $this->file;
		
		$GLOBALS['lang'] = $lang;
		
		//$c->setVar('lang', $this->file);
		//$c->save();
	}
	
	function getFileName()
	{
		return $this->file;
	}
	
	function getNearestLang()
	{		
		$country = getCountry( 
						getHostnameExt(
								getHost(
									getIp()
										)
									), 
						secureVar(@$_SERVER['HTTP_ACCEPT_LANGUAGE'])
			);
		
		foreach($this->langAvailable as $key => $langInfo)
		{
			if($langInfo[3] == $country)
			{
				return $key;
			}
		}
		return $this->langAvailable['default_lang'];
	}
	
	function getArrayLangs()
	{
		$return = array();
		
		// french, print languages in french
		if(substr($this->getFileName(), 0, 2) === 'fr')
		{
			$key = 0;
		}
		else
		{
			$key = 1;
		}
		
		foreach($this->langAvailable as $file => $a_info)
		{
			if(is_array($a_info))
			{
				$return[$file] = $a_info[$key];
			}
		}
		ksort($return);
		$return['other'] = $GLOBALS['lang']['generique_autrelangure'];
		
		return $return;
	}
	
	function getArrayTranslators()
	{
		$return = array();
		
		// french, print languages in french
		if(substr($this->getFileName(), 0, 2) === 'fr')
		{
			$key = 0;
		}
		else
		{
			$key = 1;
		}
		
		foreach($this->langAvailable as $file => $a_info)
		{
			if(is_array($a_info))
			{
				$return[] = array(
					'lang_name' => $a_info[$key],
					'lang_file' => $file,
					'translator_name' => $a_info[4],
					'translator_email' => $a_info[5]
					);
			}
		}
		
		$return[] = array(
			'lang_name' => $GLOBALS['lang']['generique_autrelangure'],
			'translator_name' => $GLOBALS['lang']['generique_vous'],
			'lang_file' => 'other',
			'translator_email' => 'matthieu@phpmyvisites.net?subject=I Want To Become A Powerfull Translator'
			);
		return $return;
	}
	
	function reloadLangFile()
	{
		
		$handle=opendir(LANGS_PATH );
		while ($file = readdir($handle)) 
		{
			if (strpos($file, '-utf-8.php'))
			
			{
				unset($lang);
				require LANGS_PATH . "/". $file;
				
				$langAvailable[$file] = array(
					$lang['lang_libelle_fr'],
					$lang['lang_libelle_en'],
					$lang['charset'],
					$lang['lang_iso'],
					$lang['auteur_nom'],
					$lang['auteur_email']
				);
			}
		}
		closedir($handle);
		
		ksort($langAvailable);
		$langAvailable['default_lang'] = LANG_DEFAULT;
		
		saveConfigFile($this->fileAdress, $langAvailable, 'langAvailable');
		
		$this->langAvailable = $langAvailable;
	}
	
	function getFontName()
	{
		if(isset($GLOBALS['languageFonts'][$GLOBALS['lang']['lang_iso']]))
		{
			return $GLOBALS['languageFonts'][$GLOBALS['lang']['lang_iso']];
		}
		else
		{
			return $GLOBALS['defaultFont'];
		}
	}
}
?>