<?php
/*
 * Extension Mapstraction pour GMap
 *
 * Auteur : Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 */

$GLOBALS['ms_cultures'] = array(
	'cs' => "cs-CZ", 		// Czech - Czech Republic
	
	'da' => "da-DK",		// Danish - Denmark
	
	'de' => "de-DE",		// German - Germany
	
	'en' => "en-US",		// English - United States
	'ga' => "en-US",
	'gd' => "en-US",
	
	'es' => "es-ES",		// Spanish - Spain
	
	'fi' => "fi-FI",		// Finnish -Finland
	
	'fr' => "fr-FR",		// French - France
	'br' => "fr-FR",
	
	'it' => "it-IT",		// Italian - Italy
	'it_fem' => "it-IT",
	
	'ja' => "ja-JP",		// Japanese - Japan
	
	'nl' => "nl-NL",		// Dutch - Netherlands
	
	'no' => "nb-NO",		// Norwegian (Bokmal) - Norway
	'nb' => "nb-NO",
	'nn' => "nb-NO",
	
	'pt' => "pt-PT",		// Portuguese - Portugal
	'pt_br' => "Pt-BR",		// Portuguese - Brazil
	
	'sv' => "sv-SE",		// Swedish - Sweden
	
);

// Transformer une langue en culture (fr-FR)
function lang2culture($lang)
{
	$code = $GLOBALS['ms_cultures'][$lang];
	if (isset($code))
		return $code;
	$lang_parts = explode("_", $lang);
	if ($lang_parts)
		$short_lang = $lang_parts[0];
	else
		$short_lang = $lang;
	$code = $GLOBALS['ms_cultures'][$short_lang];
	if (isset($code))
		return $code;
	else
		return 'en-US';
}

?>