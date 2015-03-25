<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de communication avec l'API REST du serveur Piwik
 * 
 * @param string $piwik_url Url du serveur
 * @param string $token_auth Le token d'autentification du serveur
 * @param string $module [optional]
 * @param string $method
 * @param string $format [optional]
 * @param array $options [optional]
 * @return string Le contenu de la réponse
 */
function inc_piwik_recuperer_data_dist($piwik_url,$token_auth,$module='API',$method,$format='PHP',$options=array()){
	
	$url = parametre_url($piwik_url,'token_auth',$token_auth);
	$url = parametre_url($url,'module','API','&');
	$url = parametre_url($url,'format',$format,'&');
	$url = parametre_url($url,'method',$method,'&');
	if(is_array($options)){
		foreach($options as $cle => $val){
			$url = parametre_url($url,$cle,$val,'&');
		}
	}
	
	include_spip('inc/distant');
	$content = recuperer_page($url);
	
	return $content;
}
?>