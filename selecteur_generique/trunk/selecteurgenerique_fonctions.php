<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Vérifier la présence des scripts nécessaires au sélecteur générique dans une page
 * @param string $flux Le contenu de la page
 * @return string Retourne une liste de <script> et de <link> à insérer dans le <head> de la page
 */
function selecteurgenerique_verifier_js($flux){
	include_spip('inc/plugin');
	if(spip_version_compare($spip_version_branche,"3.1.0",'<'))
		$prep = "'.$prep.'";
	$contenu = "";
		/**
		 * On a besoin de '.$prep.'autocomplete.js et de ses dépendances
		 */
		if(strpos($flux,''.$prep.'autocomplete.js')===FALSE){
			/**
			 * ui.core.js
			 */
			if(strpos($flux,''.$prep.'core.js')===FALSE){
				$ui = find_in_path('prive/javascript/ui/'.$prep.'core.js');
				$contenu .= "
<script type='text/javascript' src='$ui'></script>
";
			}
			/**
			 * ui.widget.js
			 */
			if(strpos($flux,''.$prep.'widget.js')===FALSE){
				$widget = find_in_path('prive/javascript/ui/'.$prep.'widget.js');
				$contenu .= "
<script type='text/javascript' src='$widget'></script>
";
			}
			/**
			 * ui.position.js
			 */
			if(strpos($flux,''.$prep.'position.js')===FALSE){
				$position = find_in_path('prive/javascript/ui/'.$prep.'position.js');
				$contenu .= "
<script type='text/javascript' src='$position'></script>
";
			/**
			 * Finalement on insère l'autocompleteur
			 */
			$autocompleter = find_in_path('prive/javascript/ui/'.$prep.'autocomplete.js');
			
			$contenu .= "
<script type='text/javascript' src='$autocompleter'></script>
";

			/**
			 * '.$prep.'autocomplete.html
			 * Ajoute la prise en compte de code html dans le sélecteur et l'interprète
			 * Par exemple des images / icones
			 */
			if(strpos($flux,''.$prep.'autocomplete.html')===FALSE){
				$autocompleter_html = find_in_path('javascript/autocomplete.html.js');
				$contenu .= $autocompleter_html ? "
<script type='text/javascript' src='$autocompleter_html'></script>
" : '';
			}
		};
	}
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
	if(strpos($flux,''.$prep.'autocomplete.css')===FALSE){
		/**
		 * ui.core.css
		 */
		if(strpos($flux,''.$prep.'core.css')===FALSE){
			$ui_css = find_in_path('css/'.$prep.'core.css');
			$contenu .= "
<link rel='stylesheet' href='$ui_css' type='text/css' media='all' />
";
		}
		/**
		 * ui.autocomplete.css
		 */
		if(strpos($flux,''.$prep.'autocomplete.css')===FALSE){
			$autocomplete_css = find_in_path('css/'.$prep.'autocomplete.css');
			$contenu .= "
<link rel='stylesheet' href='$autocomplete_css' type='text/css' media='all' />
";
		}

		/**
		 * ui.theme.css
		 */
		if(strpos($flux,''.$prep.'theme.css')===FALSE){
			$theme_css = find_in_path('css/'.$prep.'theme.css');
			$contenu .= "
<link rel='stylesheet' href='$theme_css' type='text/css' media='all' />
";
		}
	}
	return $contenu;
}

// critere {contenu_auteur_select} , cf. sedna
function critere_contenu_auteur_select_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];

	if (isset($crit->param[0][0])
	AND $crit->param[0][0]->texte == 'strict')
		$debut = '';
	else
		$debut = '%';

	// un peu trop rapide, ca... le compilateur exige mieux (??)
	$boucle->hash = '
	// RECHERCHE
	if ($r = _request("q")) {
		$r = _q("'.$debut.'$r%");
		$s = "(
			auteurs.nom LIKE $r
			OR auteurs.email LIKE $r'

	// on ne cherche pas dans la bio etc
	// si on peut trouver direct dans le nom ou l'email
	. (!$debut
		? ''
		: '
			OR auteurs.bio LIKE $r
			OR auteurs.nom_site LIKE $r
			OR auteurs.url_site LIKE $r
		')
	.'
		)";
	} else {
		$s = 1;
	}
	';
	$boucle->where[] = '$s';
}

?>
