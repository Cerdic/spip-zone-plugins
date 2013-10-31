<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function correction_liens_internes_pre_edition($flux){
    if ($flux['args']['action'] == "modifier"){
        foreach ($flux['data'] as $champ => $valeur){
            $flux['data'][$champ] = correction_liens_internes_correction($valeur);
            }
        }
    return $flux;
    }

function correction_liens_internes_correction($texte){
	// traiter d'autre domaines ?
	if($domaines = correction_liens_internes_autres_domaines() ){
		$domaines = array_unique(array_merge(array(url_de_base()),$domaines));
		array_walk($domaines, function(&$v) { $v = preg_quote($v); });
		$url_site = '('.join('|',$domaines).')';
	} else {
		$url_site = preg_quote(url_de_base());
	}
    // on repère les mauvaises urls
    $match=array();
    preg_match_all("#\[(.*)->($url_site(.*))\]#U",$texte,$match,PREG_SET_ORDER);
    include_spip("inc/urls");
    $type_urls = ($GLOBALS['type_urls'] === 'page'
                                AND $GLOBALS['meta']['type_urls'])
                        ?  $GLOBALS['meta']['type_urls']
                        :  $GLOBALS['type_urls'];
    foreach ($match as $lien){
        $mauvais_raccourci = $lien[0];
        $mauvaise_url = $lien[2];
        $composants_url =urls_decoder_url($mauvaise_url);
        
        if ($composants_url[0]){
            $objet      = $composants_url[1]["type"];
            $id_objet   = $composants_url[1]["id_$objet"];
            }
        
        else if ($type_urls == 'simple'){
            $composants_url =  parse_url($mauvaise_url);
            parse_str($composants_url["query"],$composants_url);
            $objet      =  $composants_url["page"];
            $id_objet   =  $composants_url["id_$objet"];
        }
        if ($objet and $id_objet){
            if ($objet == "article"){
                $objet = "";
                }
            else if ($objet == "auteur"){
                $objet = "aut";
                }
            else if ($objet =="rubrique"){
                $objet = "rub";
                }
            $bonne_url  = $objet.$id_objet;
            $bon_raccourci = str_replace($mauvaise_url,$bonne_url,$mauvais_raccourci);
            $texte = str_replace($mauvais_raccourci,$bon_raccourci,$texte);
            }
        }
    return $texte;
    }


function correction_liens_internes_autres_domaines(){
	$autres_domaines = array();
	// si la constante est définie, prendre en compte les domaines déclarés
	if( defined('CORRECTION_LIENS_INTERNES_AUTRES_DOMAINES')){
		$autres_domaines = preg_split('#([\s,|])+#i',CORRECTION_LIENS_INTERNES_AUTRES_DOMAINES);
	}
	// si le plugin multidomaine est actif, prendre en compte tous les domaines déclarés
	if(test_plugin_actif('multidomaines')){
		$config_multi = lire_config('multidomaines');
		foreach($config_multi as $key=>$value){
			if(preg_match('#editer_url#',$key) && $value) {
				$autres_domaines[] = $value;
			}
		}
	}
	// mettre en forme les domaines
	array_walk($autres_domaines, function(&$v) {
		// ajouter un slash final si nécessaire
		if(substr($v,-1)!='/'){$v = $v.'/';}
		// ajouter http:// par défaut si pas de scheme
		$infos = parse_url($v);
		if(!$infos['scheme']){
			$v = 'http://'.$v;
		}
	});
	return $autres_domaines;
}