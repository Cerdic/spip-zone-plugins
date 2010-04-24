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
			'site' => ''
	), $config);
	ecrire_config('multilang', serialize($config));

	// Insertion de la css
	$flux .= "\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('css/multilang.css')).'" type="text/css" media="all" />';
	$exec = _request('exec') ;
	$root = '' ;
	
	if($config['siteconfig'] && $exec=='configuration'){
		$root .= 'div#configurer-accueil' ; // Config Site
	} else if($config['article'] && $exec=='articles_edit') { // Articles
		$root .= 'div.cadre-formulaire-editer' ;
	} else if($config['breve'] && $exec=='breves_edit') { // Breves
		$root .= 'div.cadre-formulaire-editer' ;
	} else if($config['rubrique'] && $exec=='rubriques_edit') { // Rubriques
		$root .= 'div.cadre-formulaire-editer' ;
	} else if($config['auteur'] && $exec=='auteur_infos') { // Auteurs
		$root .= 'div.cadre-formulaire-editer' ;
	} else if($config['document'] && ($exec=='naviguer' ||
												 $exec=='articles')) {  // Docs dans page de presentation rubrique ou article,
		$root .= 'div#portfolio_portfolio,div#portfolio_documents' ; 
	} else if($config['site'] && $exec=='sites_edit') { // Sites
		$root .= 'div.cadre-formulaire-editer' ;
	} else if($config['motcle'] && ($exec=='mots_type' ||
																 $exec=='mots_edit')) { // Mots
		$root .= 'div.cadre-formulaire-editer' ;
	}

	// Docs traites a part dans pages d'edition d'articles et de rubriques
	if($config['document'] && ($exec=='rubriques_edit' || $exec=='articles_edit')){
		$root .= ',div#liste_documents,div.formulaire_editer_document' ; // avec ou sans Mediatheque
	}
	
	// Appel de multilang_init_lang si
	// - document.ready 
	// - onAjaxLoad (cas des docs et de la configuration du site)

	$flux .= '<script type="text/javascript" src="'.find_in_path("javascript/multilang.js").'"></script>
			  <script type="text/javascript">
			  var multilang_avail_langs = "'.$GLOBALS["meta"]["langues_multilingue"].'".split(\',\'),
			  multilang_def_lang = "'.$GLOBALS["meta"]["langue_site"].'",
			  dir_plugin = "'._DIR_PLUGIN_MULTILANG.'";
			  jQuery(document).ready(function(){
					function multilang_init(){
						
				';
	if($root) {
		$flux .= '      multilang_init_lang({fields:":text,textarea",root:"'.$root.'"});
					';
	}
	// Pour toutes les forms de class multilang (pour les autres plugins)
	$flux .= '     forms_selector = $(".multilangclass").parents("form") ;
					   multilang_init_lang({fields:".multilangclass",forms:forms_selector});
					} // end multilang_init
					multilang_init();
					if(typeof onAjaxLoad == "function") onAjaxLoad(multilang_init);
			  }); 
			  </script>
			  ' ;

	return $flux;
}


?>
