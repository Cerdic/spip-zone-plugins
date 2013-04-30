<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function generer_multilang_init(){
	$data = '';
	if(count($langues = explode(',',$GLOBALS["meta"]["langues_multilingue"])) > 1){
		include_spip('inc/config');
		$config = lire_config('multilang',array());
		$root = '' ;
		if(isset($config['multilang_public']))
			unset($config['multilang_public']);
		if(isset($config['multilang_crayons']))
			unset($config['multilang_crayons']);
		if(isset($config['siteconfig']) && $config['siteconfig']){
			$root .= 'div#configurer-accueil,div.formulaire_configurer_identite' ; // Config Site
			unset($config['siteconfig']);
		}
		
		foreach($config as $conf => $val){
			if($val == 'on') {
				if($conf == 'document')
					$root .= ',div#portfolio_portfolio,div#portfolio_documents,div#liste_documents,div.formulaire_editer_document' ;
				else
					$root .= ',div.formulaire_editer_'.$conf;
				unset($config[$conf]);
			}
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
					$(window).scroll(function() {
						var offset = menu_lang.parents("form").offset();
						var limite_multilang = offset.top;
						var limite_bas = limite_multilang+menu_lang.parents("form").height()-menu_lang.parents("form").find(".boutons").height();
						var pos_bas = menu_lang.offset().top+menu_lang.height();
						if(($(window).scrollTop() >= limite_multilang) && (pos_bas <= limite_bas) && ($(window).scrollTop() < limite_bas)){
							if(!menu_lang.hasClass("menu_lang_flottant"))
								menu_lang.addClass("menu_lang_flottant");
							var menu_lang_width = menu_lang.width();
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
';
	}
	return $data;
}
?>