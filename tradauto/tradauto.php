<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/translator');


// TRAITEMENT PIPELINE :
//****************************************************
function tradauto_ajout_bouton($flux)
{
 	$clientID = lire_config('tradauto/clientID');
	if (empty($clientID))
	{
		echo "ERREUR Tradauto : Le plugin n'est pas encore configuré.";
		return $flux;
	}

	$translator = new Translator();


  $flux['args']['contexte']['tradauto_token'] = urlencode($translator->get_token()); // On stocke le token dans le contexte pour réutilisation par le formulaire javascript

	include_spip('inc/cfg_config');
	$f = array("\\", "\"");
	$r =  array("\\\\", "\\\"");
	$flux['args']['contexte']['tradauto_exclus'] = trim(str_replace($f , $r, lire_config('tradauto/mt_exclus')));
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


?>