<?php

/**
 * Insertion dans le pipeline insert_head_prive
 * Ajoute css et javascript dans le <head> privé
 *
 * @param string $flux Le contenu du head privé
 */
function multilang_insert_head_prive($flux){
	$config = lire_config('multilang',array());

	$flux = multilang_inserer_head($flux,$config);

	return $flux;
}

/**
 * Insertion dans le pipeline insert_head
 * si on a configuré multilang pour s'insérer dans l'espace public
 *
 * @param $flux
 */
function multilang_insert_head($flux){
	$config = lire_config('multilang',array());

	if($config['multilang_public'] == 'on'){
		$flux .= "\n".'<link rel="stylesheet" href="'.url_absolue(generer_url_public('multilang.css')).'" type="text/css" media="all" />';
		$flux .= multilang_inserer_head($flux,$config);
	}

	return $flux;
}

/**
 * La fonction de modification du $flux pour l'insertion dans le head qu'il
 * soit privé ou public
 *
 * @param string $flux Le head de la page où l'on se trouve
 * @param array $config La configuration du plugin
 */
function multilang_inserer_head($flux,$config=array()){
	if(count(explode(',',$GLOBALS["meta"]["langues_multilingue"])) > 1){
		// Insertion de la css
		$root = '' ;

		if($config['siteconfig']){
			$root .= 'div#configurer-accueil' ; // Config Site
		}
		if($config['article']) { // Articles
			$root .= ',div.formulaire_editer_article';
		}
		if($config['breve']) { // Breves
			$root .= ',div.formulaire_editer_breve';
		}
		if($config['rubrique']) { // Rubriques
			$root .= ',div.formulaire_editer_rubrique';
		}
		if($config['auteur']) { // Auteurs
			$root .= ',div.formulaire_editer_auteur';
		}
		if($config['document']) {  // Docs dans page de presentation rubrique ou article,
			$root .= ',div#portfolio_portfolio,div#portfolio_documents' ;
		}
		if($config['site']) { // Sites
			$root .= ',div.formulaire_editer_site';
		}
		if($config['motcle']) { // Mots
			$root .= ',div.formulaire_editer_mot,div.formulaire_editer_groupe_mot';
		}

		// Docs traites a part dans pages d'edition d'articles et de rubriques
		if($config['document']){
			$root .= ',div#liste_documents,div.formulaire_editer_document' ; // avec ou sans Mediatheque
		}

		// Appel de multilang_init_lang si
		// - document.ready
		// - onAjaxLoad (cas des docs et de la configuration du site)

		$flux .= '<script type="text/javascript" src="'.generer_url_public("multilang_lang.js","lang=".$GLOBALS["spip_lang"]).'"></script>
				  	<script type="text/javascript" src="'.find_in_path("javascript/multilang.js").'"></script>
				  	<script type="text/javascript">
				  	var multilang_avail_langs = "'.$GLOBALS["meta"]["langues_multilingue"].'".split(\',\'),
				  	multilang_def_lang = "'.$GLOBALS["meta"]["langue_site"].'",
					multilang_lang_courante = "'.$GLOBALS["spip_lang"].'",
				  	multilang_dir_plugin = "'._DIR_PLUGIN_MULTILANG.'";

					// On trie les langues. Langue de l environnement en premier,
					// puis langue principale du site puis les autres en ordre alphabetique
					// Un utilisateur de langue anglaise souhaite logiquement traduire en anglais
					multilang_avail_langs = jQuery.grep(multilang_avail_langs, function(value) {
						 return (value != multilang_def_lang && value != multilang_lang_courante);
					});
					multilang_avail_langs.sort() ;
					if(multilang_lang_courante!=multilang_def_lang) multilang_avail_langs.unshift(multilang_def_lang) ;
					multilang_avail_langs.unshift(multilang_lang_courante) ;

				  	jQuery(document).ready(function(){
						function multilang_init(){
							root = "'.$root.'";
							fields_selector = "textarea,input:text:not(input#new_login,input#email,#titreparent,input.date,input.heure,input#largeur,input#hauteur,.ac_input,#url_syndic,*.nomulti),.multilang";
							// on exclue aussi les form d upload (Pour les vignettes de docs, logos...)
							forms_selector = "form[class!=\'form_upload\'][class!=\'form_upload_icon\']";
							root_opt = "form:has(.multilang)";
							fields_selector_opt = ".multilang";
							multilang_init_lang({fields:fields_selector,root:root,forms:forms_selector});
						}
						multilang_init();
						if(typeof onAjaxLoad == "function") onAjaxLoad(multilang_init);
				  });
				  </script>
				  ';
	}
	return $flux;
}
?>