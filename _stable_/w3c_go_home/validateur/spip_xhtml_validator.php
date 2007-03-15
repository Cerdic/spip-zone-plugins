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
			$ma_page = recuperer_page($url);
			if (!$ma_page)
				return array(false,"404","404");
			$sax = $transformer_xml($ma_page, false);
			$erreurs = (strlen($GLOBALS['xhtml_error'])>0);
			$texte = _T("w3cgh:erreur").substr($GLOBALS['xhtml_error'],0,50);
			$ok = false;
			if(!$erreurs){
				$ok = true;
				$texte = _T("w3cgh:page_valide");
			}	
			return array($ok,$erreurs,$texte);
		case 'visu':
			$url = parametre_url($url,'var_mode','debug','&');
			$url = parametre_url($url,'var_mode_affiche','validation','&');
			include_spip('inc/headers');
			redirige_par_entete($url);
			break;
	}
	return false;
}

?>
