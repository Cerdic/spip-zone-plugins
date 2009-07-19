<?php

function inc_piwik_recuperer_data($piwik_url,$token_auth,$module='API',$method,$format='PHP',$options=array()){
	
	$url = parametre_url($piwik_url,'token_auth',$token_auth);
	$url = parametre_url($url,'module','API','&');
	$url = parametre_url($url,'format',$format,'&');
	$url = parametre_url($url,'method',$method,'&');
	if(is_array($options)){
		foreach($options as $cle => $val){
			$url = parametre_url($url,$cle,$val,'&');
		}
	}
	
	spip_log('URL = '.$url,'piwik');
	include_spip('inc/distant');
	$content = recuperer_page($url);
	
	return $content;
}
?>