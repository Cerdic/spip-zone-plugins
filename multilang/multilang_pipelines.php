<?php

function multilang_insert_head_prive($flux){

	// Si la config existe deja, on ne l'ecrase pas
	// Si evolution de la config, les nouveaux champs sont rajoutes
	$config = lire_config('multilang');
	if (!is_array($config)) {
		$config = array();
	}
	$config = array_merge(array(
			'siteconfig' => 'on',
			'article' => '',
			'breve' => '',
			'rubrique' => 'on',
			'auteur' => 'on',
			'document' => 'on',
			'motcle' => '',
			'site' => '',
			'formstables' => ''
	), $config);
	ecrire_config('multilang', serialize($config));

	// Insertion de la css
	$flux .= "\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('css/multilang.css')).'" type="text/css" media="all" />';
	$exec = _request('exec') ;
	if($config['siteconfig'] && $exec=='configuration'){
		$flux .= multilang_traiter('div#configurer-accueil') ; // Config Site
	} else if($config['article'] && $exec=='articles_edit') { // Articles
		$flux .= multilang_traiter('div.cadre-formulaire-editer') ;
	} else if($config['breve'] && $exec=='breves_edit') { // Breves
		$flux .= multilang_traiter('div.cadre-formulaire-editer') ;
	} else if($config['rubrique'] && $exec=='rubriques_edit') { // Rubriques
		$flux .= multilang_traiter('div.cadre-formulaire-editer') ;
	} else if($config['auteur'] && $exec=='auteur_infos') { // Auteurs
		$flux .= multilang_traiter('div.cadre-formulaire-editer') ;
	} else if($config['document'] && ($exec=='naviguer' ||
																	$exec=='articles')) {  // Docs dans page de presentation rubrique ou article,
		$flux .= multilang_traiter('div#portfolio,div#documents,div.formulaire_editer_document') ; //avec ou sans Mediatheque
	} else if($config['document'] && $exec=='documents_edit') {// Mediatheque document
		$flux .= multilang_traiter('div.formulaire_editer_document') ;
	} else if($config['site'] && $exec=='sites_edit') { // Sites
		$flux .= multilang_traiter('div.cadre-formulaire-editer') ;
	} else if($config['motcle'] && ($exec=='mots_type' ||
																 $exec=='mots_edit')) { // Mots
		$flux .= multilang_traiter('div.cadre-formulaire-editer') ;
	} else if($config['formstables'] && $exec=='forms_edit'){
		$flux .= multilang_traiter('div#champs') ; // Création d'un formulaire
	} else if($config['formstables'] && $exec=='donnees_edit'){
		$flux .= multilang_traiter('div.spip_forms') ; // Remplissage d'un formulaire
	}
	// Docs traites a part dans pages d'edition d'articles et de rubriques
	if($config['document'] && ($exec=='rubriques_edit' || $exec=='articles_edit')){
		$flux .= multilang_traiter('div#liste_documents,div.formulaire_editer_document') ; // avec ou sans Mediatheque
	}
	
	return $flux;
}

function multilang_traiter($obj){

	// Appel de multilang_init_lang si
	// - document.ready 
	// - onAjaxLoad (cas des docs et de la configuration du site)

	$out = '<script type="text/javascript" src="'.find_in_path("javascript/multilang.js").'"></script>
			  <script type="text/javascript">
			  var multilang_avail_langs = "'.$GLOBALS["meta"]["langues_multilingue"].'".split(\',\'),
			  multilang_def_lang = "'.$GLOBALS["meta"]["langue_site"].'";
			  jQuery(document).ready(function(){
					function multilang_init(){
						multilang_init_lang({fields:":text,textarea",root:"'.$obj.'"});
					} 
					multilang_init();
					if(typeof onAjaxLoad == "function") onAjaxLoad(multilang_init);
			  }); 
			  </script>' ;

	return $out;
}
?>
