<?php
/**
 * Fichier de fonctions de translate.html
 *
 * @plugin     Translate Yandex
 * @copyright  2018
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Translate_yandex\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

	include_spip('lib/Yandex/src/Translator');
	include_spip('lib/Yandex/src/Translation');
	include_spip('lib/Yandex/src/Exception');
	
	use Yandex\Translate\Translator;
	use Yandex\Translate\Translation;
	use Yandex\Translate\Exception;

	
/*
 * La fonction traduire est issue de seenthis
 * elle utilise la librairie Yandex téléchargeable sur 
 *
 *
 *
**/
function traduire($text,$dest,$source){
	
	include_spip('inc/config');
	$key = lire_config('translate_yandex/key_yandex');
	if(!$key){
		return "<i>Aucune traduction n'est possible car il manque la clef d'autorisation, merci de contacter le ou la webmaster.</i>";
	}
	
	try {
	  $translator = new Translator($key);
	  $translation = $translator->translate($text, "$source-$dest");
	
	  return $translation;
	
	  //echo $translation->getSource();
	  //echo $translation->getSourceLanguage();
	  //echo $translation->getResultLanguage();
	  
	  
	} catch (Exception $e) {
	  // handle exception
	}

};