<?php
function trad_rub_header_prive($flux){

    $flux .= '<link rel="stylesheet" href="'.find_in_path('css/trad_rub_styles.css').'" type="text/css" media="all" />';
 	return $flux;	

 }
 
/*Ajoute la langue de traduction dans le chargement du formulaire edition_article*/ 
 function trad_rub_formulaire_charger($flux){
   $form = $flux['args']['form'];
   if ($form=='editer_rubrique'){

	$flux['data']['lang_dest'] .= _request('lang_dest');
			
	$flux['data']['_hidden'] .= '<input type="hidden" name="lang_dest" value="'._request('lang_dest').'"/>';
	if($version = $GLOBALS['spip_version_branche']>=3) $flux['data']['_hidden'] .= '<input type="hidden" name="changer_lang" value="'._request('lang_dest').'"/>';	
		
    }
    return $flux;
}

/*Ajoute le id traduction a la rubrique d'origine*/ 
 function trad_rub_formulaire_traiter($flux){
    $form = $flux['args']['form'];
   if ($form=='editer_rubrique'){
   	$id_trad=_request('lier_trad');
	if($id_trad){
		sql_updateq('spip_rubriques',array('id_trad'=>$id_trad),'id_rubrique='.$id_trad);
		}
    }
    return $flux;
}
/*Prise en compte de la langue de traduction dans le traitement du formulaire edition_article*/ 
 function trad_rub_pre_insertion($flux){
    if ($flux['args']['table']=='spip_rubriques'){
    
		if($lang=_request('lang_dest')){
			$id_trad=_request('lier_trad');
			$flux['data']['lang'] =  $lang;
			$flux['data']['langue_choisie'] =  'oui';
			$flux['data']['id_trad'] =  $id_trad;						 	
			}
    	}
return $flux;
}


function trad_rub_recuperer_fond($flux){
	//Insertion des onglets de langue

    if ($flux['args']['fond'] == 'prive/squelettes/contenu/rubrique'){

    	$contexte=array('id_rubrique'=> $flux['args']['contexte']['id_rubrique']);
				
		$barre_langue=recuperer_fond("prive/editer/barre_traductions_rubrique",$contexte,array('ajax'=>true));

        $flux['data']['texte'] = str_replace('</h1>', '</h1>' . $barre_langue, $flux['data']['texte']);
    }


 return $flux;   
}
?>
