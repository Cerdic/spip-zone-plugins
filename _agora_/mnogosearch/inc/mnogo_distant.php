<?php

function mnogo_querystring(){
	$default_qs=array('q'=>'','m'=>'all','wm'=>'wrd','sp'=>1,'sy'=>1,'wf'=>'2221','type'=>'','ul'=>'','fmt'=>'xml','np'=>0,'ps'=>10,'GroupBySite'=>'no');
	$key_translate = array('recherche'=>'q','site'=>'ul','debut'=>'np');

	foreach($_REQUEST as $key=>$value){
		if (isset($key_translate[$key]))
			$key = $key_translate[$key];
		if (isset($default_qs[$key]))
			$default_qs[$key] = $value;
	}
	$default_qs['fmt'] = 'xml'; // obligatoire
	$req = "";
	foreach($default_qs as $key=>$value)
		$default_qs[$key]=$key."=".$value;
	return implode("&",$default_qs);
}

function mnogo_getresults(){	
	global $mnogo_resultats_synthese;
	global $mnogo_resultats;
	$url = isset($GLOBALS['meta']['mnogo_url_search'])?$GLOBALS['meta']['mnogo_url_search']:"";
	$qs = mnogo_querystring();
	$url .= (strpos($url,"?")!==FALSE)?"&$qs":"?$qs";

	$arbre = array();
	include_spip('inc/distant');
	$contenu = recuperer_page($url);
	if ($contenu){
		include_spip('inc/plugin');
		$arbre = parse_plugin_xml($contenu);
	}
	if (isset($arbre['recherche'][0])){
		foreach ($arbre['recherche'][0] as $balise=>$value){
			if ($balise!='resultats')
				$mnogo_resultats_synthese[preg_replace(',^balise_,i','',$balise)] = applatit_arbre($value);
			/*if ($balise=='balise_MNOGO_TOTAL')
				$mnogo_resultats = array_fill (0, $mnogo_resultats_synthese['MNOGO_TOTAL'], NULL );*/
		}
		if (isset($arbre['recherche'][0]['resultats'][0]['resultat'])){
			foreach ($arbre['recherche'][0]['resultats'][0]['resultat'] as $key=>$liste){
				foreach ($liste as $balise=>$value) {
					$mnogo_resultats[$key][preg_replace(',^balise_,i','',$balise)] = applatit_arbre($value);
				}
			}
		}
	}
}

?>