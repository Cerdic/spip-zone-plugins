<?php

function SelecteurGenerique_inserer_auteur() {

	$ac = generer_url_ecrire('selecteur_generique');
	$id_article = _request('id_article');
	$statut = _request('statut');
	if (_request('exec') == 'auteurs'){
		$input = 'input[@name=recherche][autocomplete!=off]';
	}
	else{
		$input = 'input[@name=cherche_auteur][autocomplete!=off]';
	}
	return <<<EOS

(function($) {
	var appliquer_selecteur_cherche_auteur = function() {
		// chercher l'input de saisie
		var me = jQuery('$input');
		me.autocomplete('$ac',
			{
				extraParams:{quoi:'auteur',id_article:'$id_article',statut:'$statut'},
				delay: 300,
				autofill: false,
				minChars: 1,
				//'helperClass': 'autocompleter',
				//'selectClass': 'selectAutocompleter',
				formatItem: function(data, i, n, value) {
					return data[0];
				},
				formatResult: function(data, i, n, value) {
					return data[1];
				},
			}
		);
		me.result(function(event, data, formatted) {
			if (data[2] > 0) {
				me.attr('name', 'old_value')
				.parents('form')
				.append(
					jQuery("<input type='hidden' name='nouv_auteur' value='"+data[2]+"' />"
					)
				)
				.find("input[@type=submit]")
					.click()
				.end();
			}
		});
	};
	$(function(){
		appliquer_selecteur_cherche_auteur();
		onAjaxLoad(appliquer_selecteur_cherche_auteur);
	});
})(jQuery);
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

	$ac = generer_url_ecrire('selecteur_generique');
	return <<<EOS

(function($) {
	var appliquer_selecteur_cherche_mot = function() {
		// chercher l'input de saisie
		var me = jQuery('input[@name=cherche_mot][autocomplete!=off]');
		me.each(function(){
			var inp = this;
			var id_groupe = jQuery(this).parents('form').find('input[@name=select_groupe]').val();
			jQuery(inp).autocomplete('$ac',{
				extraParams:{quoi:'mot',$type:'$id',id_groupe:''+id_groupe+''},
				delay: 300,
				autofill: false,
				minChars: 1,
				//'helperClass': 'autocompleter',
				//'selectClass': 'selectAutocompleter',
				formatItem: function(data, i, n, value) {
					return data[0];
				},
			});
			jQuery(inp).result(function(event, data, formatted) {
				if (data[2] > 0) {
					jQuery(inp)
					.attr('name', 'old_value')
					.parents('form')
					.append(
						jQuery("<input type='hidden' name='nouv_mot' value='"+data[2]+"' />")
					).find('input[@type=submit]')
					.click()
					.end();
				}
				else{
					return data[1];
				}
			});
		});
	};
	$(function(){
		appliquer_selecteur_cherche_mot();
		onAjaxLoad(appliquer_selecteur_cherche_mot);
	});
})(jQuery);
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
	OR _request('exec') == 'acces_restreint_edit'
	OR _request('exec') == 'auteurs') {
		$js .= SelecteurGenerique_inserer_auteur();
	}
	
	if (_request('exec') == 'articles'
	OR _request('exec') == 'naviguer'
	OR _request('exec') == 'breves_voir'
	OR _request('exec') == 'sites') {
		$js .= SelecteurGenerique_inserer_mot();
	}
	if ($js)
		$js =

		'<script type="text/javascript" src="'
		. find_in_path('javascript/jquery.autocomplete.js')
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