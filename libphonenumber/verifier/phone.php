<?php
/**
 * vérification des numéros internationaux
 * 
 * @plugin     libphonenumber for SPIP
 * @copyright  2019
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * (c) 2019 - Distribue sous licence GNU/GPL
 *
**/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Vérifie un numéro de téléphone avec la lib https://github.com/giggsey/libphonenumber-for-php
 * 
 *
 * @param string $valeur
 *   La valeur à vérifier.
 * @param array $options
 *   pays
 * @return string
 *   Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */

	include_spip('lib/libphonenumber/src/MatcherAPIInterface');
	include_spip('lib/libphonenumber/src/MetadataLoaderInterface');
	include_spip('lib/libphonenumber/src/DefaultMetadataLoader');
	include_spip('lib/libphonenumber/src/MetadataSourceInterface');
	include_spip('lib/libphonenumber/src/MultiFileMetadataSourceImpl');
	include_spip('lib/libphonenumber/src/PhoneNumberUtil');
	include_spip('lib/libphonenumber/src/CountryCodeToRegionCodeMap');
	include_spip('lib/libphonenumber/src/RegexBasedMatcher');
	include_spip('lib/libphonenumber/src/PhoneNumber');
	include_spip('lib/libphonenumber/src/CountryCodeSource');
	include_spip('lib/libphonenumber/src/Matcher');
	include_spip('lib/libphonenumber/src/PhoneMetadata');
	include_spip('lib/libphonenumber/src/PhoneNumberDesc');
	include_spip('lib/libphonenumber/src/NumberFormat');
	include_spip('lib/libphonenumber/src/PhoneNumberType');
	include_spip('lib/libphonenumber/src/ValidationResult');
	include_spip('lib/libphonenumber/src/NumberParseException');
	include_spip('lib/libphonenumber/src/PhoneNumberFormat');
	

	use libphonenumber\MatcherAPIInterface;
	use libphonenumber\MetadataLoaderInterface;
	use libphonenumber\DefaultMetadataLoader;
	use libphonenumber\MetadataSourceInterface;
	use libphonenumber\MultiFileMetadataSourceImpl;
	use libphonenumber\PhoneNumberUtil;
	use libphonenumber\PhoneNumber;
	use libphonenumber\RegexBasedMatcher;
	use libphonenumber\CountryCodeToRegionCodeMap;
	use libphonenumber\CountryCodeSource;
	use libphonenumber\Matcher;
	use libphonenumber\PhoneMetadata;
	use libphonenumber\PhoneNumberDesc;
	use libphonenumber\NumberFormat;
	use libphonenumber\PhoneNumberType;
	use libphonenumber\ValidationResult;
	use libphonenumber\NumberParseException;
	use libphonenumber\PhoneNumberFormat;


function verifier_phone_dist($valeur, $options = array()) { 
	$ok = '';
	
	$erreur = _T('verifier:erreur_telephone');
	if (!is_string($valeur) OR strlen($valeur) < 8 ) {
		return $erreur;
	}
	
	$clean_number = str_replace("|","",$valeur);
	if($valeur != $clean_number){
		return $erreur." Merci de retirer le signe |";
	}
	
	$pays = $options['prefixes_pays']; //"CH"
	
	$NumberStr = $valeur ;//"044 668 18 00";
	$phoneUtil = libphonenumber\PhoneNumberUtil::getInstance();
	try {
		$NumberProto = $phoneUtil->parse($NumberStr, $pays);
		//var_dump($NumberProto);
	} catch (libphonenumber\NumberParseException $e) {
		//var_dump($e);
		return $e;
	}
	
	$isValid = $phoneUtil->isValidNumber($NumberProto);
	//var_dump($isValid); // true →1
	if(!$isValid){
		$erreur = "Attention à l'écriture du numéro";
		return $erreur;
	}
	//on va peut-être garder l'international pour la construction du form Elavon
	$international = $phoneUtil->format($NumberProto, \libphonenumber\PhoneNumberFormat::INTERNATIONAL);
	
	if($valeur != $international){
		$erreur = "Erreur dans l'internationalisation du numéro"; //.$NumberProto['nationalNumber'];
		$erreur .= " essayez ". $phoneUtil->format($NumberProto, \libphonenumber\PhoneNumberFormat::INTERNATIONAL);
		return $erreur;
	}
   
   return $ok;
}