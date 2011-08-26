<?php
function taa_header_prive($flux){

    $flux .= '<link rel="stylesheet" href="'.find_in_path('css/taa_styles.css').'" type="text/css" media="all" />';
 	return $flux;	

 }
 
/*Ajoute la langue de traduction dans le chargement du formulaire edition_article*/ 
 function taa_formulaire_charger($flux){
    $form = $flux['args']['form'];
   if ($form=='editer_article'){

	$flux['data']['lang_dest'] .= _request('lang_dest');		
	$flux['data']['_hidden'] .= '<input type="hidden" name="lang_dest" value="'._request('lang_dest').'"/>';
    }
    return $flux;
}


/*Prise en compte de la langue de traduction dans le traitement du formulaire edition_article*/ 
function taa_pre_insertion($flux){
    if ($flux['args']['table']=='spip_articles'){
		if($lang=_request('lang_dest')){
			$flux['data']['lang'] =  $lang;
			$flux['data']['langue_choisie'] =  $lang;		 	
			}
		elseif(test_plugin_actif('tradrub')){
			$lang=sql_getfetsel('lang','spip_rubriques','id_rubrique='.sql_quote(_request('id_rubrique')));
			$flux['data']['lang'] =  $lang;
			$flux['data']['langue_choisie'] =  $lang;	
			}
    	}
return $flux;
}

/*Modifie l'affichage de l'article dans l'espace interne*/
 function taa_afficher_fiche_objet($flux){
    $type = $flux['args']['type'];

   if ($type=='article'){
	$id_article= _request('id_article');
	$barre=charger_fonction('barre_langues','inc');
	$barre_langue=$barre($id_article);
	
	$flux['data'] =preg_replace('/<div class=\'boite_onglets\'/',$barre_langue.'<div id=\'boite_onglets\'',$flux['data']);
	}
return $flux;
}


?>
