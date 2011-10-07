<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Vérifier la présence des scripts nécessaires au sélecteur générique dans une page
 * @param string $flux Le contenu de la page
 */
function selecteurgenerique_verifier_js($flux){
	global $spip_version_branche;
	$contenu = "";
	/**
	 * Si on est dans une version récente, on utilise jquery ui
	 */
	if(defined('_DIR_PLUGIN_JQUERYUI') && ($spip_version_branche >= '2.1.10')){
		/**
		 * On a besoin de jquery.ui.autocomplete.js et de ses dépendances
		 */
		if(strpos($flux,'jquery.ui.autocomplete.js')===FALSE){
			/**
			 * ui.core.js
			 */
			if(strpos($flux,'jquery.ui.core.js')===FALSE){
				$ui = find_in_path(_DIR_JQUERYUI_JS.'jquery.ui.core.js');
				$contenu .= "
<script type='text/javascript' src='$ui'></script>
";
			}
			/**
			 * ui.widget.js
			 */
			if(strpos($flux,'jquery.ui.core.js')===FALSE){
				$widget = find_in_path(_DIR_JQUERYUI_JS.'jquery.ui.widget.js');
				$contenu .= "
<script type='text/javascript' src='$widget'></script>
";
			}
			/**
			 * ui.position.js
			 */
			if(strpos($flux,'jquery.ui.position.js')===FALSE){
				$position = find_in_path(_DIR_JQUERYUI_JS.'jquery.ui.position.js');
				$contenu .= "
<script type='text/javascript' src='$position'></script>
";
			}
			/**
			 * Finalement on insère l'autocompleteur
			 */
			$autocompleter = find_in_path('javascript/jquery.autocomplete.js');
			
			$contenu .= "
<script type='text/javascript' src='$autocompleter'></script>
";
		};
		
		/**
		 * On insère également les fonctions de bases supplémentaires
		 */
		if(strpos($flux,'selecteur_generique_functions')===FALSE){
			$functions = find_in_path('javascript/selecteur_generique_functions.js');
			$contenu .= "
<script type='text/javascript' src='$functions'></script>
";
		};
		
		/**
		 * On intègre la CSS qui va bien également et ses dépendances
		 */
		if(strpos($flux,'jquery.ui.autocomplete.css')===FALSE){
			/**
			 * ui.core.css
			 */
			if(strpos($flux,'jquery.ui.core.css')===FALSE){
				$ui_css = find_in_path(_DIR_JQUERYUI_CSS.'ui.core.css');
				$contenu .= "
<link rel='stylesheet' href='$ui_css' type='text/css' media='all' />
";
			}
			/**
			 * ui.autocomplete.css
			 */
			if(strpos($flux,'jquery.ui.autocomplete.css')===FALSE){
				$autocomplete_css = find_in_path(_DIR_JQUERYUI_CSS.'jquery.ui.autocomplete.css');
				$contenu .= "
<link rel='stylesheet' href='$autocomplete_css' type='text/css' media='all' />
";
			}

			/**
			 * ui.theme.css
			 */
			if(strpos($flux,'jquery.ui.theme.css')===FALSE){
				$theme_css = find_in_path(_DIR_JQUERYUI_CSS.'jquery.ui.theme.css');
				$contenu .= "
<link rel='stylesheet' href='$theme_css' type='text/css' media='all' />
";
			}
		}
	}else if($spip_version_branche < '2.1.10'){
	    if(strpos($flux,'jquery.autocomplete.js')===FALSE){
			$autocompleter = find_in_path('javascript/jquery.autocomplete.js');
			$autocompletecss = find_in_path('iautocompleter.css');
			$contenu .= "
<script type='text/javascript' src='$autocompleter'></script>
<link rel='stylesheet' href='$autocompletecss' type='text/css' media='all' />
	";
		};
	}
	return $contenu;
}
?>