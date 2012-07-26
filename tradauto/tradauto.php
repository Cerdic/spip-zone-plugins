<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/translator');


// TRAITEMENT PIPELINE :
//****************************************************
function tradauto_ajout_bouton($flux)
{
	$translator = new Translator();


  $flux['args']['contexte']['tradauto_token'] = urlencode($translator->get_token()); // On stocke le token dans le contexte pour réutilisation par le formulaire javascript

	include_spip('inc/cfg_config');
	$flux['args']['contexte']['tradauto_exclus'] = str_replace('"', '\"', lire_config('tradauto/mt_exclus'));
	$flux['args']['contexte']['tradauto_lang'] = (array)$translator->GetLanguages($flux['args']['contexte']['lang']); //$translator->GetLanguagesForTranslate();

//	if ($flux['args']['type']=='article')
	{
//print_r($flux['args']['contexte']); exit;
	  $form = recuperer_fond('formulaires/ajout_bouton', $flux['args']['contexte']);
		$flux['data'] = $form.$flux['data'];
	}

//print_r($flux); exit;
	return $flux;
}










/*



	    //Create the authorization Header string.
	    $authHeader = "Authorization: Bearer ". $accessToken;

	    //Create the Translator Object.
	    $translatorObj = new HTTPTranslator();

	    //Input String.
	    $inputStr = 'This is the sample string.';
	    //HTTP Detect Method URL.
	    $detectMethodUrl = "http://api.microsofttranslator.com/V2/Http.svc/Detect?text=".urlencode($inputStr);
	    //Call the curlRequest.
	    $strResponse = $translatorObj->curlRequest($detectMethodUrl, $authHeader);
	    //Interprets a string of XML into an object.
	    $xmlObj = simplexml_load_string($strResponse);
	    foreach((array)$xmlObj[0] as $val){
	        $languageCode = $val;
	    }

	    /*
	     * Get the language Names from languageCodes.
	     */
/*
	    $locale = 'en';
	    $getLanguageNamesurl = "http://api.microsofttranslator.com/V2/Http.svc/GetLanguageNames?locale=$locale";
	    //Create the Request XML format.
	    $requestXml = $translatorObj->createReqXML($languageCode);
	    //Call the curlRequest.
	    $curlResponse = $translatorObj->curlRequest($getLanguageNamesurl, $authHeader, $requestXml);

	    //Interprets a string of XML into an object.
	    $xmlObj = simplexml_load_string($curlResponse);
	    echo "<table border=2px>";
	    echo "<tr>";
	    echo "<td><b>LanguageCodes</b></td><td><b>Language Names</b></td>";
	    echo "</tr>";
	    foreach($xmlObj->string as $language){
	        echo "<tr><td>".$inputStr."</td><td>". $languageCode."(".$language.")"."</td></tr>";
	    }
	    echo "</table>";
	} catch (Exception $e) {
	    echo "Exception: " . $e->getMessage() . PHP_EOL;
	}

*/






?>