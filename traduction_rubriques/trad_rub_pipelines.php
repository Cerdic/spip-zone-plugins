<?php
function trad_rub_header_prive($flux){

    $flux .= '<link rel="stylesheet" href="'.find_in_path('css/trad_rub_styles.css').'" type="text/css" media="all" />';
 	return $flux;	

 }
 
/*Ajoute la langue de traduction dans le chargement du formulaire edition_article*/ 
 function trad_rub_formulaire_charger($flux){
    $form = $flux['args']['form'];
   if ($form=='editer_rubrique'){
   	$id_trad=_request('lier_trad');
	$flux['data']['lang_dest'] .= _request('lang_dest');
	if($id_trad AND $flux['data']['lang_dest']){
		$trads=donnees_traduction($flux['data']['lang_dest'],$id_trad);	
		$flux['data']['_hidden'] .= '<input type="hidden" name="id_parent" value="'.$trads['id_parent'].'"/>';
		$flux['data']['_hidden'] .= '<input type="hidden" name="id_secteur" value="'.$trads['id_secteur'].'"/>';		
		}
	
	
	$flux['data']['_hidden'] .= '<input type="hidden" name="lang_dest" value="'._request('lang_dest').'"/>';
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
			$flux['data']['lang'] =  'de';
			$flux['data']['langue_choisie'] =  'oui';
			$flux['data']['id_trad'] =  $id_trad;					 	
			}
    	}
return $flux;
}

/*Modifie l'affichage de la rubrique dans l'espace interne*/
 function trad_rub_afficher_contenu_objet($args){
    if ($args["args"]["type"] == "rubrique") {
    	$contenu .= recuperer_fond("prive/barre_traductions",
            array('id_rubrique' => $args["args"]["id_objet"]));
    	$contenu .= recuperer_fond('prive/contenu/rubrique',$args['args']['contexte']);
        $args["data"] = $contenu;
    }
    return $args;}

?>
