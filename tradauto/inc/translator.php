<?php

include_spip('inc/microsoft_translator');
include_spip('inc/cfg_config');

class Translator
{
	const URL_AUTH = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
	const URL_SCOPE = "http://api.microsofttranslator.com"; //Application Scope Url
	const	URL_GetLanguagesForTranslate = "http://api.microsofttranslator.com/V2/Http.svc/GetLanguagesForTranslate";
	const URL_GetLanguageNames = "http://api.microsofttranslator.com/V2/Http.svc/GetLanguageNames";
	const URL_Translate = "http://api.microsofttranslator.com/V2/Http.svc/Translate";

	const GRANT_TYPE = "client_credentials"; //Application grant type

	public $clientID; //Client ID of the application.
	public $clientSecret;	//Client Secret key of the application.
	public $accessToken;
	public $mt; // Microsoft Translator object
  public $authHeader;



	//**********************************************************
	// Constructeur : initialise les paramètres trouvés dans CFG
	//**********************************************************
	function __construct()
	{
  	$this->clientID = lire_config('tradauto/clientID');
		if (empty($this->clientID)) echo "ERREUR class Translator : Le ID client n'est pas configuré.";

		$this->clientSecret = lire_config('tradauto/clientSecret');
		if (empty($this->clientSecret)) echo "ERREUR plass Translator : Le secret client n'est pas configuré.";

		$this->get_token();
		$this->mt = new HTTPTranslator();
	}


	//*********************************************************
	// Demande un jeton d'accès au service Microsoft Translator
	//*********************************************************
	function get_token()
	{
		try
		{
			$authObj = new AccessTokenAuthentication();
			$this->accessToken = $authObj->getTokens(self::GRANT_TYPE, self::URL_SCOPE, $this->clientID, $this->clientSecret, self::URL_AUTH);
    	$this->authHeader = "Authorization: Bearer ". $this->accessToken;
		}
		catch (Exception $e) {
			echo "Class Translator:get_token : Exception pendant la demande de jeton: " . $e->getMessage() . PHP_EOL;
		}

		return $this->accessToken;
	}


	//*************************************************************************************
	// Renvoie une liste des codes des langues prise en charge par le service de traduction
	//*************************************************************************************
	function GetLanguagesForTranslate()
	{
		try
		{
			//Call Curl Request.//
			$strResponse = $this->mt->curlRequest(self::URL_GetLanguagesForTranslate, $this->authHeader);

			// Interprets a string of XML into an object.
			$xmlObj = simplexml_load_string($strResponse);

			$languageCodes = array();
			foreach($xmlObj->string as $language)
   	    $languageCodes[] = $language;

			return $languageCodes;

		} catch (Exception $e) {
		    echo "Class Translator:GetLanguagesForTranslate : Exception: " . $e->getMessage() . PHP_EOL;
		}
	}


	//***************************************************************************************
	// Renvoie une liste des langues en fonction d'une liste de code langue,
	// et dans la langue locale
	//***************************************************************************************
	function GetLanguageNames($languageCodes, $locale)
	{
		try
		{
	    //Create the XML string for passing the values.
	    $requestXml = '<ArrayOfstring xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
	    if(sizeof($languageCodes) > 0)
			{
	        foreach($languageCodes as $codes)
	        $requestXml .= "<string>$codes</string>";
	    } else {
	        throw new Exception('$languageCodes array is empty.');
	    }
	    $requestXml .= '</ArrayOfstring>';

			//Call Curl Request.//
			$strResponse = $this->mt->curlRequest(self::URL_GetLanguageNames."?locale=".$locale, $this->authHeader, $requestXml);
//print_r($strResponse);
			// Interprets a string of XML into an object.
			$xmlObj = simplexml_load_string($strResponse);

			$languageCodes = array();
			foreach($xmlObj->string as $language)
   	    $languages[] = (string)$language;

			return $languages;

		} catch (Exception $e) {
		    echo "Class Translator:GetLanguageNames : Exception: " . $e->getMessage() . PHP_EOL;
		}
	}


	//***************************************************************************************
	// Renvoie un tableau contenant les langues disponibles pour la traduction
	// la clé est le code langue, la valeur est le nom litéral
	//***************************************************************************************
	function GetLanguages($locale)
	{
		$languageCodes = $this->GetLanguagesForTranslate();
    $languages = $this->GetLanguageNames($languageCodes, $locale);
		return array_combine($languageCodes, $languages);
	}

	//***************************************************************************************
	// Traduit une chaine $inputStr dans une langue $fromLanguage vers une langue $toLanguage
	//***************************************************************************************
	function Translate($inputStr, $fromLanguage, $toLanguage, $contentType='text/plain')
	{

		try
		{
    	$params = "text=".urlencode($inputStr)."&to=$toLanguage&from=$fromLanguage&contentType=$contentType";
			//Call Curl Request.//
			$strResponse = $this->mt->curlRequest(self::URL_Translate."?$params", $this->authHeader);

			// Interprets a string of XML into an object.
			$xmlObj = simplexml_load_string($strResponse);
	    foreach((array)$xmlObj[0] as $val){
	        $translatedStr = $val;
	    }

			return $translatedStr;

		} catch (Exception $e) {
		    echo "Class Translator:Translate : Exception: " . $e->getMessage() . PHP_EOL;
		}
	}


}

?>
