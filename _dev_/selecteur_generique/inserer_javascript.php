<?php

function SelecteurGenerique_inserer_javascript($flux) {

$js = '';

if (_request('exec') == 'articles') {

	$ac = parametre_url(generer_url_public('selecteur_generique_auteur'),
		'id_article', _request('id_article'), '\\x26');

	$js .= '<script type="text/javascript"><!--'
		. <<<EOS

var appliquer_selecteur_cherche_auteur = function() {

	// chercher l'input de saisie
	var inp = jQuery('input[@name=cherche_auteur]', this);

	// ne pas reappliquer si on vient seulement de charger les suggestions
	if (!inp[0] || inp[0].autoCFG) return;

	// attacher l'autocompleter
	inp.Autocomplete({
		'source': '$ac',
		'delay': 300,
		'autofill': false,
		'helperClass': 'autocompleter',
		'selectClass': 'selectAutocompleter',
		'minchars': 2,
		'onSelect': function(li) {
			if (li.id > 0) {
				inp.attr('name', 'old_value')
				.parents('form')
				.append(
					jQuery("<input type='hidden' name='nouv_auteur' value='"+li.id+"' />"
					)
				)
				.ajaxSubmit() // FF 1.5 exige ajaxSubmit(), mais FF 2 et Safari font un POST sans retour
				.trigger('submit'); // celui-ci fait un POST avec retour sous FF2 et Safari
			}
		}
	});
}

// Premier chargement
jQuery(document).ready(appliquer_selecteur_cherche_auteur);

// Chargements ajax suivants
// setTimeout obligatoire sinon on s'applique au vieux DOM 
// pre-update quand on clique sur une suppression d'auteur !!
onAjaxLoad(function(){setTimeout(appliquer_selecteur_cherche_auteur, 200);});


EOS
		. '// --></script>'
		. "\n";

}


if (_request('exec') == 'articles' /* ou breves etc */) {

	$ac = parametre_url(generer_url_public('selecteur_generique_mot'),
		'id_article', _request('id_article'), '\\x26');


	$js .= '<script type="text/javascript"><!--'
		. <<<EOS

var appliquer_selecteur_cherche_mot = function() {

	// chercher l'input de saisie
	var inp = jQuery('input[@name=cherche_mot]', this);

	// ne pas reappliquer si on vient seulement de charger les suggestions
	if (!inp[0] || inp[0].autoCFG) return;

	// attacher l'autocompleter
	inp.each(function() {
		var me = this;
		var id_groupe = jQuery(me).parents('form').find('input[@name=select_groupe]').val();
		
		jQuery(this)
		.Autocomplete({
			'source': '$ac'+'\x26id_groupe='+id_groupe,
			'delay': 300,
			'autofill': false,
			'helperClass': 'autocompleter',
			'selectClass': 'selectAutocompleter',
			'minchars': 2,
			'onSelect': function(li) {
				if (li.id > 0) {
					jQuery(me)
					.attr('name', 'old_value')
					.parents('form')
					.append(
						jQuery("<input type='hidden' name='nouv_mot' value='"+li.id+"' />"
						)
					)
					.ajaxSubmit() // FF 1.5 exige ajaxSubmit(), mais FF 2 et Safari font un POST sans retour
					.trigger('submit'); // celui-ci fait un POST avec retour sous FF2 et Safari
				}
			}
		});
	});
}

// Premier chargement
jQuery(document).ready(appliquer_selecteur_cherche_mot);

// Chargements ajax suivants
onAjaxLoad(function(){setTimeout(appliquer_selecteur_cherche_mot, 200);});


EOS
		. '// --></script>'
		. "\n";





}


	$base = !$js
		? ''
		: (

		'<script type="text/javascript" src="'
		. find_in_path('javascript/iautocompleter.js')
		. '"></script>'
		. "\n"

		. '<script type="text/javascript" src="'
		. find_in_path('javascript/interface.js')
		. '"></script>'
		. "\n"

		. '<link rel="stylesheet" type="text/css" '
		. 'href="'.find_in_path('jquery.autocomplete.css').'" />'
		. "\n"

		);

	return $flux.$base.$js;
}

?>
