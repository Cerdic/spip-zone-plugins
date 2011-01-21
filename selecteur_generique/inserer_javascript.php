<?php

function SelecteurGenerique_inserer_auteur() {

	$ac = generer_url_ecrire('selecteur_generique');
	$id_article = _request('id_article');
	$statut = _request('statut');
	$input = 'input[name=cherche_auteur][autocomplete!=off]';

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
				width: 'auto',
				formatItem: function(data, i, n, value) {
					return data[0];
				},
				formatResult: function(data, i, n, value) {
					return data[1];
				}
			}
		);
		me.result(function(event, data, formatted) {
			if (data[2] > 0) {
				me.attr('name', 'old_value')
				.parents('form')
				.append(
					jQuery("<input type='hidden' name='nouv_auteur' value='"+data[2]+"' />" // signature SPIP 1.9
					+"<input type='hidden' name='auteur_article_${id_article}_new' value='"+data[2]+"' />"  // signature SPIP 2.0
					)
				)
				.find("input[type=submit]")
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
		var groupes = new Array;
		var groupes_titre = new Array;
		var hide=false;
		$('input[name=cherche_mot][autocomplete!=off]')
		.each(function(){
			var id_groupe = $(this).parents('form').find('input[name=select_groupe]').val();
			groupes.push(id_groupe);
			groupes_titre.push($(this).val());
			if (hide)
				$(this).parent().hide();
			hide=true;
		});
		hide=false;
		$('input[name=cherche_mot][autocomplete!=off]:first')
		.each(function(){
			var inp = this;
			$(this)
			.val(groupes_titre.join(', '))
			.attr('title',groupes_titre.join(', '));
			$(this)
			.parents('form')
			.bind('submit', function(){
				$('.ac_results').remove();
			})
			.find('input[name=select_groupe]')
			.val(groupes.join(','));
			$(inp).autocomplete('$ac',{
				extraParams:{quoi:'mot',$type:'$id',groupes: groupes.join(',')},
				delay: 300,
				autofill: false,
				minChars: 1,
				selectFirst: false,
				formatItem: function(data, i, n, value) {
					return data[0];
				},
			});
			$(inp).result(function(event, data, formatted) {
				if (data[2] > 0) {
					$(inp)
					.attr('name', 'old_value')
					.parents('form')
					.append(
						$("<input type='hidden' name='nouv_mot' value='"+data[2]+"' />")
					).find('input[type=submit]')
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
	$ac = generer_url_ecrire('selecteur_generique');

	return <<<EOS
(function($) {
	var appliquer_selecteur_cherche_rubrique = function() {

		// chercher l'input de saisie
		var me = jQuery('input[id=titreparent]');
		me.each(function(){
			var inp = this;
			var id_groupe = jQuery(this).parents('form').find('input[name=select_groupe]').val();

			jQuery(inp).focus(function(){
				$(this).val('');
			});
			jQuery(inp).attr('disabled','').autocomplete('$ac',{
				extraParams:{quoi:'rubrique'},
				delay: 300,
				autofill: false,
				minChars: 1,
				formatItem: function(data, i, n, value) {
					return data[0];
				},
			});
			jQuery(inp).result(function(event, data, formatted) {
				if (data[2] > 0) {
					jQuery(inp)
					.val(data[1])
					.parents('form').find('input[id=id_parent]')
					.val(data[2])
					.end();
				}
				else{
					return data[1];
				}
			});
		});
	};
	$(function(){
		appliquer_selecteur_cherche_rubrique();
		onAjaxLoad(appliquer_selecteur_cherche_rubrique);
	});
})(jQuery);
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
	OR _request('exec') == 'rubriques_edit') {
		$js .= SelecteurGenerique_inserer_rubrique();
	}
	if ($js)
		$js =

		'<script type="text/javascript" src="'
		. find_in_path('javascript/jquery.autocomplete.js')
		. '"></script>'
		. "\n"

		. '<link rel="stylesheet" type="text/css" '
		. 'href="'.find_in_path('iautocompleter.css').'" media="all" />'
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