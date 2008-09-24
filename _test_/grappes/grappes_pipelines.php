<?php

/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

function grappes_inserer_js_recherche_objet(){
	return <<<EOS
		
	function rechercher_objet(id_selecteur, page_selection) {

		// chercher l'input de saisie
		jQuery(id_selecteur+' input[@name=nom_objet]')
		.not('[@autoCFG]')
		.each(function() {
			var me = this;
			jQuery(this)
			.Autocomplete({
				'source': page_selection,
				'delay': 200,
				'autofill': false,
				'helperClass': "autocompleter",
				'selectClass': "selectAutocompleter",
				'minchars': 1,
				'mustMatch': true,
				'inputWidth': false,
				'cacheLength': 20,
				'multiple' : false,
				'multipleSeparator' : ";",
				fx : {type: "fade", duration: 400},
				'onShow' : function(suggestionBox, suggestionIframe) {
					jQuery('.autocompleter, .selectAutocompleter').fadeTo(300,1);
				},
				'onSelect': 
				function(li) {
					if (li.id > 0) {
						var id = li.id;
						jQuery(id_selecteur + ' #pid_objet').val(id);
						jQuery(id_selecteur + ' input[type="submit"]').focus();
						jQuery(me)
						.end();
					}
				}
			});
		});
	}
EOS;
}
function grappes_inserer_javascript($flux){
	
	$js = grappes_inserer_js_recherche_objet();
	$js = 
		'<script type="text/javascript" src="'
		. find_in_path('javascript/iautocompleter.js')
		. '"></script>'
		. "\n"

		. '<script type="text/javascript" src="'
		. find_in_path('javascript/iutil.js')
		. '"></script>'
		. "\n"

		. '<link rel="stylesheet" type="text/css" '
		. 'href="'.find_in_path('iautocompleter.css').'" />'
		. "\n"

		. '<script type="text/javascript"><!--'
		. "\n"
		. $js
		. "\n"
		. '// --></script>'
		. "\n";

	return $flux.$js;	
}

?>
