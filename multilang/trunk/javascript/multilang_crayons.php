<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Renvoie le code javascript pour utiliser multilang avec les crayons
 *
 * Uniquement si plusieurs langues, espace public activé, ainsi que les crayons.
 *
 * @param Array $config
 * @return string
 */
function multilang_javascript_crayons($config) {

	$javascript = '';
	$root = array();
	$formulaires = is_array($config['formulaires']) ? array_filter($config['formulaires']) : array();
	if (isset($formulaires['siteconfig']) && $formulaires['siteconfig']) {
		$root[] = 'input[type=hidden][name*=name_][value|=meta-valeur]';
	}
	foreach ($formulaires as $formulaire) {
		if ($formulaire == 'gis') {
			// Les points gis sont traités bizarrement dans les crayons qui enlèvent
			// purement et simplement leur 's'
			// TODO : c'est sans doute dû à objet_type, qu'il faudrait appliquer partout ici aussi
			$formulaire = 'gi';
		}
		$root[] = 'input[type=hidden][name*=name_][value|='.$formulaire.']:not(input[value|='.$formulaire.'-logo]):not(input[value|='.$formulaire.'-vignette]):not(input[value|='.$formulaire.'-fichier])';
	}
	$javascript = '
		var crayons_multilang_init = function(){
			if(typeof(multilang_init_lang) == "function"){
				var crayons_root = ".formulaire_spip:has('.implode(',', $root).')",
					fields_selector = "textarea,input:text:not(input.date,input.heure,*.nomulti)",
					forms_selector = "form[class!=\'form_upload\'][class!=\'form_upload_icon\']",
					root_opt = "form:has(.multilang)",
					fields_selector_opt = ".multilang";
				multilang_init_lang({fields:fields_selector,fields_opt:fields_selector_opt,root:crayons_root,root_opt:root_opt,forms:forms_selector,init_done:false});
			}
		}

		cQuery(document).ready(function(){
			if(typeof onAjaxLoad == "function") onAjaxLoad(crayons_multilang_init);
			crayons_multilang_init();
		});';

	return $javascript;
}
