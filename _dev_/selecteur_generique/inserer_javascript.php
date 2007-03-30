<?php

function SelecteurGenerique_inserer_javascript($flux) {

if (_request('exec') == 'articles') {

	$flux .= '<script type="text/javascript" src="'
		. find_in_path('javascript/iautocompleter.js')
		. '"></script>'
		. "\n";

	$flux .= '<script type="text/javascript" src="'
		. find_in_path('javascript/interface.js')
		. '"></script>'
		. "\n";

	$ac = parametre_url(generer_url_public('selecteur_generique'),
		'id_article', _request('id_article'), '&');

	$flux .= '<script type="text/javascript"><!--'
		. <<<EOS

var appliquer_selecteur_cherche_auteur = function(doc) {
	var inp = jQuery('input[@name=cherche_auteur]', doc);
	inp.Autocomplete({
		'source': '$ac',
		'delay': 300,
		'autofill': false,
		'helperClass': 'autocompleter',
		'selectClass': 'selectAutocompleter',
		'minchars': 2,
		'onSelect': function(li) {
			inp.attr("name", "nouv_auteur")
			.val(li.id_auteur)
			.parents("form")
			.trigger('submit');
		}
	});
}
var appliquer_selecteur_cherche_auteur_ajax = function(doc) {
	console.log(doc);
	if (typeof doc == 'undefined')
		return;
	else
		return appliquer_selecteur_cherche_auteur(doc);
}

jQuery(document).ready(appliquer_selecteur_cherche_auteur);
onAjaxLoad(appliquer_selecteur_cherche_auteur_ajax);


EOS
		. '// --></script>'
		. "\n";


	$flux .= '
<link rel="stylesheet" type="text/css" href="'.find_in_path('jquery.autocomplete.css').'" />
';

}

	return $flux;
}

?>
