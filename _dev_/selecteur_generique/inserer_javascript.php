<?php

function SelecteurGenerique_inserer_auteur() {

	$ac = parametre_url(generer_url_public('selecteur_generique_auteur'),
		'id_article', _request('id_article'), '\\x26');

	return <<<EOS

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
				.find("input[@type=submit]")
					.click()
				.end();
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


EOS;

}

function SelecteurGenerique_inserer_mot() {
	if ($id = _request('id_article')) {
		$type = 'id_article';
	} elseif ($id = _request('id_syndic')) {
		$type = 'id_syndic';
	} else {
		$id = _request('id_breve');
		$type = 'id_breve';
	}

	$ac = parametre_url(generer_url_public('selecteur_generique_mot'),
		$type, $id, '\\x26');


	return <<<EOS

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
					.find("input[@type=submit]")
						.click()
					.end();
				}
			}
		});
	});
}

// Premier chargement
jQuery(document).ready(appliquer_selecteur_cherche_mot);

// Chargements ajax suivants
onAjaxLoad(function(){setTimeout(appliquer_selecteur_cherche_mot, 200);});


EOS;

}


function SelecteurGenerique_inserer_rubrique() {

	$ac = parametre_url(generer_url_public('selecteur_generique_rubrique'),
		'id_article', _request('id_article'), '\\x26');

	return <<<EOS

var appliquer_selecteur_cherche_rubrique = function() {

	// chercher l'input de saisie
	var inp = jQuery('input#titreparent', this);

	// ne pas reappliquer si on vient seulement de charger les suggestions
	if (!inp[0] || inp[0].autoCFG) return;

	// attacher l'autocompleter
	inp
	.Autocomplete({
		'source': '$ac',
		'delay': 300,
		'autofill': false,
		'helperClass': 'autocompleter',
		'selectClass': 'selectAutocompleter',
		'minchars': 2,
		'onSelect': function(li) {
			if (li.id > 0) {
				inp
				.attr('original', inp.attr('value'))
				.parents('form')
				.find('input[@name=id_parent]')
					. attr('value', li.id)
				.end();
			} else {
				inp.attr('value', inp.attr('original'));
			}
		}
	})
	.attr('disabled', false)
	.attr('original', inp.attr('value'))
	.bind('focus', function() {
		this.select();
	});
}

// Premier chargement
jQuery(document).ready(appliquer_selecteur_cherche_rubrique);

// Chargements ajax suivants (pas pertinent pour le selecteur de rubriques dans articles_edit)
//onAjaxLoad(function(){setTimeout(appliquer_selecteur_cherche_rubrique, 200);});


EOS;
}

// Calcule et insere le javascript necessaire pour la page
function SelecteurGenerique_inserer_javascript($flux) {

	$js = '';

	if (_request('exec') == 'articles') {
		$js .= SelecteurGenerique_inserer_auteur();
	}

	if (_request('exec') == 'articles'
	OR _request('exec') == 'breves_voir'
	OR _request('exec') == 'sites') {
		$js .= SelecteurGenerique_inserer_mot();
	}

	if (_request('exec') == 'articles_edit'
	OR _request('exec') == 'sites_edit') {
		$js .= SelecteurGenerique_inserer_rubrique();
	}

	if ($js)
		$js =

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

		. '<script type="text/javascript"><!--'
		. "\n"
		. $js
		. "\n"
		. '// --></script>'
		. "\n";

	return $flux.$js;

}

?>
