<?php

function SelecteurGenerique_inserer_auteur() {

	$ac = parametre_url(generer_url_ecrire('selecteur_generique',
		'quoi=auteur'),
		'id_article', _request('id_article'), '\\x26');

	return <<<EOS

var appliquer_selecteur_cherche_auteur = function() {

	// chercher l'input de saisie
	jQuery('input[@name=cherche_auteur]')
	.not('[@autoCFG]')
	.Autocomplete({
		'source': '$ac',
		'delay': 300,
		'autofill': false,
		'helperClass': 'autocompleter',
		'selectClass': 'selectAutocompleter',
		'minchars': 1,
		'mustMatch': true,
		'cacheLength': 20,
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

jQuery(document).ready(function(){
	setInterval(appliquer_selecteur_cherche_auteur,2000);
});

EOS;

}

function SelecteurGenerique_inserer_mot() {
	if ($id = _request('id_article')) {
		$type = 'id_article';
	} elseif ($id = _request('id_rubrique')) {
		$type = 'id_rubrique';
	} elseif ($id = _request('id_syndic')) {
		$type = 'id_syndic';
	} else {
		$id = _request('id_breve');
		$type = 'id_breve';
	}

	$ac = parametre_url(generer_url_ecrire('selecteur_generique',
		'quoi=mot'),
		$type, $id, '\\x26');


	return <<<EOS

var appliquer_selecteur_cherche_mot = function() {

	// chercher l'input de saisie
	jQuery('input[@name=cherche_mot]')
	.not('[@autoCFG]')
	.each(function() {
		var me = this;
		var id_groupe = jQuery(me).parents('form').find('input[@name=select_groupe]').val();
		
		jQuery(this)
		.Autocomplete({
			'source': '$ac'+'\x26id_groupe='+id_groupe,
			'delay': 300,
			'autofill': false,
			'helperClass': 'autocompleter',
			'selectClass': 'selectAutocompleter',
			'minchars': 1,
			'mustMatch': true,
		//	'multiple': true,
			'cacheLength': 20,
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
					.end()
					;
				}
			}
		});
	});
}

jQuery(document).ready(function(){
	setInterval(appliquer_selecteur_cherche_mot,2000);
});


EOS;

}


function SelecteurGenerique_inserer_rubrique() {

	$ac = parametre_url(generer_url_ecrire('selecteur_generique',
		'quoi=rubrique'),
		'id_article', _request('id_article'), '\\x26');

	return <<<EOS

var appliquer_selecteur_cherche_rubrique = function() {

	// chercher l'input de saisie
	jQuery('input#titreparent')
	.not('[@autoCFG]')
	.Autocomplete({
		'source': '$ac',
		'delay': 300,
		'autofill': false,
		'helperClass': 'autocompleter',
		'selectClass': 'selectAutocompleter',
		'minchars': 1,
		'mustMatch': true,
		'cacheLength': 20,
		'onSelect': function(li) {
			if (li.id > 0) {
				inp
				.attr('original', inp.attr('value'))
				.parents('form')
				.find('input[@name=id_parent]')
					.attr('value', li.id)
					.trigger('change') // auteur_infos a un bind('change')
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

jQuery(document).ready(function(){
	setInterval(appliquer_selecteur_cherche_rubrique,2000);
});


EOS;
}

// Calcule et insere le javascript necessaire pour la page
function SelecteurGenerique_inserer_javascript($flux) {

	if (defined('DESACTIVER_SELECTEUR_GENERIQUE')
	AND DESACTIVER_SELECTEUR_GENERIQUE)
		return $flux;

	$js = '';

	if (_request('exec') == 'articles'
	OR _request('exec') == 'acces_restreint_edit') {
		$js .= SelecteurGenerique_inserer_auteur();
	}

	if (_request('exec') == 'articles'
	OR _request('exec') == 'naviguer'
	OR _request('exec') == 'breves_voir'
	OR _request('exec') == 'sites') {
		$js .= SelecteurGenerique_inserer_mot();
	}

	if (_request('exec') == 'articles_edit'
	OR _request('exec') == 'auteur_infos'
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
