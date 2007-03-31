<?php

function SelecteurGenerique_inserer_javascript($flux) {

$js = '';

if (_request('exec') == 'articles') {

	$js .= '<script type="text/javascript" src="'
		. find_in_path('javascript/iautocompleter.js')
		. '"></script>'
		. "\n";

	$js .= '<script type="text/javascript" src="'
		. find_in_path('javascript/interface.js')
		. '"></script>'
		. "\n";

	$ac = parametre_url(generer_url_public('selecteur_generique'),
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
			if (li.id_auteur > 0) {
				inp.attr('name', 'nom_nouv_auteur')
				.parents('form')
				.append(
					jQuery("<input type='hidden' name='nouv_auteur' value='"+li.id_auteur+"' />"
					)
				)
				.ajaxSubmit() // FF 1.5 exige ajaxSubmit(), mais FF 2 et Safari font un POST sans retour
				.trigger('submit'); // celui-ci fait un POST avec retour sous FF2 et Safari :(
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


	$js .= '
<link rel="stylesheet" type="text/css" href="'.find_in_path('jquery.autocomplete.css').'" />
';

}

	return $flux.$js;
}

?>
