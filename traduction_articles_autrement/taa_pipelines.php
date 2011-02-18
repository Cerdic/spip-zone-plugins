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
	$contenu_article=charger_fonction('article_afficher_contenu','inc');
	$affichage=$contenu_article($id_article);
	return $affichage;
	}
return $flux;
}

// affichage du formulaire de téléchargement des docs
function taa_affiche_gauche($flux){
	$exec= $flux['args']['exec'];
	$id = $flux['args']['id_article'];
	
	if(test_plugin_actif('medias') or test_plugin_actif('gest_doc')) $mediatheque='ok';
	
	if($exec=='articles' AND $mediatheque AND lire_config('taa/edition_seule') AND autoriser('joindredocument','article',$id)){
		$flux['data'] .= recuperer_fond('prive/editer/colonne_documents_taa',array('objet'=>'article','id_objet'=>$id));
		}

return $flux;
}

?>
