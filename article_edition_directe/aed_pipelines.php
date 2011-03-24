<?php

function aed_header_prive($flux){
    	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/aed_styles.css').'" type="text/css" media="all" />';
	return $flux;	
 }

/*Modifie l'affichage de l'article dans l'espace interne*/
 function aed_afficher_fiche_objet($flux){
    $type = $flux['args']['type'];

   if ($type=='article'){
	$id_article= _request('id_article');
	$row = sql_fetsel("*", "spip_articles", "id_article=$id_article");
	
	$contexte = array(
		'icone_retour'=>icone_inline(_T('icone_retour'), $oups, "article-24.gif", "rien.gif",$GLOBALS['spip_lang_left']),
		'redirect'=>generer_url_ecrire("articles"),
		'titre'=>$row['titre'],
		'new'=>$new?$new:$id_article,
		'id_rubrique'=>$row['id_rubrique'],
		'id_secteur'=>$row['id_secteur'],
		'config_fonc'=>'articles_edit_config',
		// passer row si c'est le retablissement d'une version anterieure
		'row'=> $id_version
		? $row
		: null
		);
	
	$formulaire=recuperer_fond('prive/editer/article_mod',$contexte);
	
	$flux['data'] =preg_replace('/<div id=\'props\' class=\'tabs-container\'>/',$formulaire.'<div id="props" class="tabs-container">',$flux['data']);
	
	}
return $flux;
}

// affichage du formulaire de téléchargement des docs
function aed_affiche_gauche($flux){
	$exec= $flux['args']['exec'];
	$id = $flux['args']['id_article'];
	
	if(test_plugin_actif('medias') or test_plugin_actif('gest_doc')) $mediatheque='ok';
	
	if($exec=='articles' AND $mediatheque AND autoriser('joindredocument','article',$id)){
		$flux['data'] .= recuperer_fond('prive/editer/colonne_documents_aed',array('objet'=>'article','id_objet'=>$id));
		}

return $flux;
}

?>
