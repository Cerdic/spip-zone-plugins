<?php

function mnogo_querystring(){
	$default_qs=array('q'=>'','m'=>'bool','wm'=>'wrd','sp'=>1,'sy'=>1,'wf'=>'2221','type'=>'','ul'=>'','fmt'=>'xml','np'=>0,'ps'=>10,'GroupBySite'=>'no');
	$key_translate = array('recherche'=>'q','site'=>'ul');

	foreach($_REQUEST as $key=>$value){
		if ($key=='debut_t'){
			$default_qs['np'] = (int)round($value/$default_qs['ps']);
		}
		if (isset($key_translate[$key]))
			$key = $key_translate[$key];
		if (isset($default_qs[$key]))
			$default_qs[$key] = $value;
	}
	$default_qs['fmt'] = 'xml'; // obligatoire
	// remplacer les operateurs ET,AND,OR,OU par leur forme & |
	$default_qs['q'] = preg_replace(',\s(ET|AND)\s,',urlencode(' & '),$default_qs['q']);
	$default_qs['q'] = preg_replace(',\s(OU|OR)\s,',urlencode(' | '),$default_qs['q']);
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

	$offset = 0;

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
			if ($balise=='balise_MNOGO_TOTAL')
				$mnogo_resultats = array_fill (0, $mnogo_resultats_synthese['MNOGO_TOTAL'], NULL );
			if ($balise=='balise_MNOGO_PREMIER')
				$offset = (int)trim(applatit_arbre($value))-1;
		}
		if (isset($arbre['recherche'][0]['resultats'][0]['resultat'])){
			foreach ($arbre['recherche'][0]['resultats'][0]['resultat'] as $key=>$liste){
				foreach ($liste as $balise=>$value) {
					$mnogo_resultats[$key+$offset][preg_replace(',^balise_,i','',$balise)] = applatit_arbre($value);
				}
			}
		}
	}
}

?>