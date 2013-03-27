<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * si on a configuré multilang pour s'insérer dans l'espace public
 *
 * @param string $flux Le contenu du head CSS
 * @return string $flux Le contenu du head CSS modifié
 */
function multilang_insert_head_css($flux){
	include_spip('inc/config');
	$config = lire_config('multilang',array());
	if($config['multilang_public'] == 'on'){

		static $done = false;

		if (!$done) {
			$done = true;
			$flux .= '<link rel="stylesheet" href="'.url_absolue(generer_url_public('multilang.css')).'" type="text/css" media="all" />';
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head_prive (SPIP)
 * Ajoute css et javascript dans le <head> privé
 *
 * @param string $flux Le contenu du head
 * @return string $flux Le contenu du head modifié
 */
function multilang_insert_head_prive($flux){
	include_spip('inc/config');
	$config = lire_config('multilang',array());

	$flux .= multilang_inserer_head($config);

	return $flux;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * si on a configuré multilang pour s'insérer dans l'espace public
 *
 * @param string $flux Le contenu du head
 * @return string $flux Le contenu du head modifié
 */
function multilang_insert_head($flux){
	include_spip('inc/config');
	$config = lire_config('multilang',array());

	if($config['multilang_public'] == 'on'){
		$flux .= multilang_insert_head_css(''); // au cas ou il n'est pas implemente
		$flux .= multilang_inserer_head($config);
	}
	return $flux;
}

/**
 * La fonction de modification du $flux pour l'insertion dans le head qu'il
 * soit privé ou public
 *
 * @param array $config La configuration du plugin
 * @return string $data Le contenu textuel qui sera inséré dans le head
 */
function multilang_inserer_head($config=array()){
	if(count($langues = explode(',',$GLOBALS["meta"]["langues_multilingue"])) > 1){

		$root = '' ;

		if(isset($config['siteconfig']) && $config['siteconfig']){
			$root .= 'div#configurer-accueil,div.formulaire_configurer_identite' ; // Config Site
		}
		if(isset($config['article']) && $config['article']) { // Articles
			$root .= ',div.formulaire_editer_article';
		}
		if(isset($config['breve']) && $config['breve']) { // Breves
			$root .= ',div.formulaire_editer_breve';
		}
		if(isset($config['rubrique']) && $config['rubrique']) { // Rubriques
			$root .= ',div.formulaire_editer_rubrique';
		}
		if(isset($config['auteur']) && $config['auteur']) { // Auteurs
			$root .= ',div.formulaire_editer_auteur';
		}
		if(isset($config['document']) && $config['document']) {  // Docs dans page de presentation rubrique ou article,
			$root .= ',div#portfolio_portfolio,div#portfolio_documents' ;
		}
		if(isset($config['site']) && $config['site']) { // Sites
			$root .= ',div.formulaire_editer_site';
		}
		if(isset($config['evenement']) && $config['evenement']) { // Evenements
			$root .= ',div.formulaire_editer_evenement';
		}
		if(isset($config['motcle']) && $config['motcle']) { // Mots
			$root .= ',div.formulaire_editer_mot,div.formulaire_editer_groupe_mot';
		}
		if(isset($config['gis']) && $config['gis']) { // GIS
			$root .= ',div.formulaire_editer_gis';
		}

		// Docs traites a part dans pages d'edition d'articles et de rubriques
		if($config['document']){
			$root .= ',div#liste_documents,div.formulaire_editer_document' ; // avec ou sans Mediatheque
		}
		// Appel de multilang_init_lang si
		// - document.ready
		// - onAjaxLoad (cas des docs et de la configuration du site)
		if(is_array($langues_config = lire_config('multilang/langues_utilisees','aucune')) && count($langues_config) > 0){
			$langues = implode(',',array_intersect($langues,$langues_config));
		}else{
			$langues = implode(',',$langues);
		}
		$data = '
<script type="text/javascript" src="'.generer_url_public("multilang_lang.js","lang=".$GLOBALS["spip_lang"]).'"></script>
<script type="text/javascript" src="'.find_in_path("javascript/multilang.js").'"></script>
<script type="text/javascript">/* <![CDATA[ */
	var multilang_avail_langs = "'.$langues.'".split(\',\'),
	multilang_def_lang = "'.$GLOBALS["meta"]["langue_site"].'",
	multilang_lang_courante = "'.$GLOBALS["spip_lang"].'",
	multilang_dir_plugin = "'._DIR_PLUGIN_MULTILANG.'";

	// On trie les langues. Langue de l environnement en premier,
	// puis langue principale du site puis les autres en ordre alphabetique
	// Un utilisateur de langue anglaise souhaite logiquement traduire en anglais
	multilang_avail_langs = jQuery.grep(multilang_avail_langs, function(value) {
		return (value != multilang_def_lang && value != multilang_lang_courante);
	});
	multilang_avail_langs.sort();
	multilang_avail_langs.unshift(multilang_lang_courante);
	if(multilang_lang_courante!=multilang_def_lang) multilang_avail_langs.unshift(multilang_def_lang);

	jQuery(document).ready(function(){
		function multilang_init(){
			var root = "'.$root.'";
			var fields_selector = "textarea:not(textarea#adresses_secondaires,textarea#repetitions),input:text:not(input#new_login,input#email,#titreparent,input.date,input.heure,input#largeur,input#hauteur,.ac_input,#url_syndic,#url_auto,#champ_geocoder,#champ_lat,#champ_lon,#champ_zoom,#places,*.nomulti),.multilang";
			// on exclue aussi les form d upload (Pour les vignettes de docs, logos...)
			var forms_selector = "form[class!=\'form_upload\'][class!=\'form_upload_icon\']";
			// Les div qui ont un formulaire de classe multilang (pour accélérer la recherche dans le DOM,
			// on passe le form et le parent sera trouvé dans lors de l\'init)
			var root_opt = "form:has(.multilang)";
			var fields_selector_opt = ".multilang";
			multilang_init_lang({fields:fields_selector,fields_opt:fields_selector_opt,root:root,root_opt:root_opt,forms:forms_selector});
			
			if($(".menu_multilang").length > 0){
				$(".menu_multilang").each(function(){
					var menu_lang = $(this);
					var menu_lang_width = menu_lang.width();
					$(window).scroll(function() {
						var offset = menu_lang.parents("form").offset();
						var limite_multilang = offset.top;
						var limite_bas = limite_multilang+menu_lang.parents("form").height()-menu_lang.parents("form").find(".boutons").height();
						var pos_bas = menu_lang.offset().top+menu_lang.height();
						if(($(window).scrollTop() >= limite_multilang) && (pos_bas <= limite_bas) && ($(window).scrollTop() < limite_bas)){
							if(!menu_lang.hasClass("menu_lang_flottant"))
								menu_lang.addClass("menu_lang_flottant");
							menu_lang.css({"position": "fixed", "top": 0, "width": menu_lang_width+"px","z-index":"999"});
						}
						if(($(window).scrollTop() < limite_multilang)||(pos_bas > limite_bas) )
							menu_lang.removeClass("menu_lang_flottant").css({"position": "static", "width": "auto"});
					});
				});
			}
		}
		multilang_init();
		if(typeof onAjaxLoad == "function") onAjaxLoad(multilang_init);
	});
/* ]]> */</script>
';
	}
	return $data;
}

/**
 * Insertion dans le pipeline affichage_final (SPIP)
 * 
 * Sur la page crayons.js, on insère également notre javascript pour être utilisable
 * dans les crayons
 * 
 * @param string $flux Le contenu de la page
 * @return string $flux Le contenu de la page modifiée 
 */
function multilang_affichage_final($flux){
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'crayons.js'){
		$root = '' ;
		include_spip('inc/config');
		$config = lire_config('multilang',array());
		if(($config['multilang_public'] == 'on') && ($config['multilang_crayons'] == 'on')){
			if(isset($config['siteconfig']) && $config['siteconfig']){
				$root .= ',input[type=hidden][name*=name_][value|=meta-valeur]';
			}
			if(isset($config['article']) && $config['article']) { // Articles
				$root .= ',input[type=hidden][name*=name_][value|=article]:not(input[value|=article-logo])';
			}
			if(isset($config['breve']) && $config['breve']) { // Breves
				$root .= ',input[type=hidden][name*=name_][value|=breve]:not(input[value|=breve-logo])';
			}
			if(isset($config['rubrique']) && $config['rubrique']) { // Rubriques
				$root .= ',input[type=hidden][name*=name_][value|=rubrique]:not(input[value|=rubrique-logo])';
			}
			if(isset($config['auteur']) && $config['auteur']) { // Auteurs
				$root .= ',input[type=hidden][name*=name_][value|=auteur]:not(input[value|=auteur-logo])';
			}
			if(isset($config['document']) && $config['document']) {  // Docs dans page de presentation rubrique ou article,
				$root .= ',input[type=hidden][name*=name_][value|=document]:not(input[value|=document-vignette])' ;
			}
			if(isset($config['site']) && $config['site']) { // Sites
				$root .= ',input[type=hidden][name*=name_][value|=site]';
			}
			if(isset($config['evenement']) && $config['evenement']) { // Evenements
				$root .= ',input[type=hidden][name*=name_][value|=evenement]:not(input[value|=evenement-logo])';
			}
			if(isset($config['motcle']) && $config['motcle']) { // Mots
				$root .= ',input[type=hidden][name*=name_][value|=mot]:not(input[value|=mot-logo])';
			}
			if(isset($config['gis']) && $config['gis']) { // GIS
				$root .= ',input[type=hidden][name*=name_][value|=gis]:not(input[value|=gis-logo])';
			}
			$texte = '
				var crayons_multilang_init = function(){
					var crayons_root = ".formulaire_spip:has('.$root.')";
					var fields_selector = "textarea,input:text:not(input.date,input.heure,*.nomulti)";
					var forms_selector = "form[class!=\'form_upload\'][class!=\'form_upload_icon\']";
					var root_opt = "form:has(.multilang)";
					var fields_selector_opt = ".multilang";
					multilang_init_lang({fields:fields_selector,fields_opt:fields_selector_opt,root:crayons_root,root_opt:root_opt,forms:forms_selector,init_done:false});
				}
				
				cQuery(document).ready(function(){
					if(typeof onAjaxLoad == "function") onAjaxLoad(crayons_multilang_init);
					crayons_multilang_init();
				});';
			$flux .= $texte;
		}
	}
	return $flux;
}
?>