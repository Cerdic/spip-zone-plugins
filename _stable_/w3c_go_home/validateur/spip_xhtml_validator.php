<?php
// validation xhtml validator
include_spip('inc/distant');

function validateur_spip_xhtml_validator_dist($action, $url= ""){
	
	$url = str_replace("&amp;","&",$url);
	$transformer_xml=charger_fonction('valider_xml', 'inc');
	switch ($action){
		case 'infos':
			return "SPIP XHTML Validator";
			break;
		case 'test':
			$page = recuperer_page($url);
			$transformer_xml($page, false);
			$erreurs = (strlen($GLOBALS['xhtml_error'])>0);
			$texte = "Erreur";
			$ok = false;
			if(!$erreurs){
				$ok = true;
				$texte = "OK";
			}	
			return array($ok,$erreurs,$texte);
		case 'visu':
			$page = recuperer_page($url);
			return $transformer_xml($page, false);
	}
	return false;
}

?>