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
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	$multilang_public = lire_config('multilang/multilang_public','off');
	if($multilang_public == 'on'){
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
 * @param string $flux 
 * 		Le contenu du head
 * @return string $flux 
 * 		Le contenu du head modifié
 */
function multilang_insert_head_prive($flux){
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	$config = lire_config('multilang',array());

	$flux .= multilang_inserer_head($config);

	return $flux;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * si on a configuré multilang pour s'insérer dans l'espace public
 *
 * @param string $flux 
 * 		Le contenu du head
 * @return string $flux 
 * 		Le contenu du head modifié
 */
function multilang_insert_head($flux){
	if(!function_exists('lire_config'))
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
	/**
	 * N'activer multilang que si plus d'une langue dans le site
	 */
	if(count($langues = explode(',',$GLOBALS["meta"]["langues_multilingue"])) > 1){
		$data = '
<script type="text/javascript" src="'.generer_url_public("multilang.js","lang=".$GLOBALS["spip_lang"]).'"></script>
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
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'crayons.js' && (count($langues = explode(',',$GLOBALS["meta"]["langues_multilingue"])) > 1)){
		if(!function_exists('lire_config'))
			include_spip('inc/config');
		$config = lire_config('multilang',array());

		/**
		 * On n'utilise multilang que si l'espace public est activé ainsi que les crayons
		 */
		if(($config['multilang_public'] == 'on') && ($config['multilang_crayons'] == 'on')){
			unset($config['multilang_public']);
			unset($config['multilang_crayons']);
			$root = array();

			if(isset($config['siteconfig']) && $config['siteconfig']){
				$root[] = 'input[type=hidden][name*=name_][value|=meta-valeur]';
				unset($config['siteconfig']);
			}

			foreach($config as $conf => $val){
				if($val == 'on') { // Articles
					$root[] = 'input[type=hidden][name*=name_][value|='.$conf.']:not(input[value|='.$conf.'-logo])';
					unset($config[$conf]);
				}
			}
			$texte = '
				var crayons_multilang_init = function(){
					if(typeof(multilang_init_lang) == "function"){
						var crayons_root = ".formulaire_spip:has('.implode(",",$root).')",
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
			$flux .= $texte;
		}
	}
	return $flux;
}
?>