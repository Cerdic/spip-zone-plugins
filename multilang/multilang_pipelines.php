<?php

/**
 * Insertion dans le pipeline insert_head_prive
 * Ajoute css et javascript dans le <head> privé
 *
 * @param string $flux Le contenu du head privé
 */
function multilang_insert_head_prive($flux){
	$config = lire_config('multilang');


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
	} else if($config['document'] && ($exec=='naviguer' || $exec=='articles')) {  // Docs dans page de presentation rubrique ou article,
		$root .= 'div#portfolio_portfolio,div#portfolio_documents' ;
	} else if($config['site'] && $exec=='sites_edit') { // Sites
		$root .= 'div.cadre-formulaire-editer' ;
	} else if($config['motcle'] && ($exec=='mots_type' || $exec=='mots_edit')) { // Mots
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
		$flux .= 'multilang_init_lang({fields:"textarea,input:text:not(input#id_parent,input.password,input#new_login,#titreparent)",root:"'.$root.'"});';
	}

	// Pour toutes les forms de class multilang (pour les autres plugins)
	$flux .= 'forms_selector = $(".multilangclass").parents("form") ;
					   multilang_init_lang({fields:".multilangclass",forms:forms_selector});
					} // end multilang_init
					multilang_init();
					if(typeof onAjaxLoad == "function") onAjaxLoad(multilang_init);
			  });
			  </script>
			  ' ;

	return $flux;
}

function multilang_insert_head($flux){
	$config = lire_config('multilang');

	if($config['multilang_public'] == 'on'){
		// Insertion de la css
		$flux .= "\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('css/multilang.css')).'" type="text/css" media="all" />';
		$root = '' ;

		if($config['siteconfig']){
			$root .= 'div#configurer-accueil' ; // Config Site
		}
		if($config['article']) { // Articles
			$root .= ',div.formulaire_editer_article' ;
		}
		if($config['breve']) { // Breves
			$root .= ',div.formulaire_editer_breve' ;
		}
		if($config['rubrique']) { // Rubriques
			$root .= ',div.formulaire_editer_rubrique' ;
		}
		if($config['auteur']) { // Auteurs
			$root .= ',div.cadre-formulaire-editer' ;
		}
		if($config['document']) {  // Docs dans page de presentation rubrique ou article,
			$root .= ',div#portfolio_portfolio,div#portfolio_documents' ;
		}
		if($config['site']) { // Sites
			$root .= ',div.cadre-formulaire-editer' ;
		}
		if($config['motcle']) { // Mots
			$root .= ',div.cadre-formulaire-editer' ;
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
			$flux .= 'multilang_init_lang({fields:":text,textarea",root:"'.$root.'"});';
		}

		// Pour toutes les forms de class multilang (pour les autres plugins)
		$flux .= 'forms_selector = $(".multilangclass").parents("form") ;
						   multilang_init_lang({fields:".multilangclass",forms:forms_selector});
						} // end multilang_init
						multilang_init();
						if(typeof onAjaxLoad == "function") onAjaxLoad(multilang_init);
				  });
				  </script>
				  ' ;
	}

	return $flux;
}
?>