(function($) {
	$(document).ready(function(){
		var minidoc = function() {
			$('#portfolios h3:not(:has(.minidoc))').each(function () {
				var titre = $(this);
				var liste = titre.next('.liste_items.documents');

				var identifiant = liste.attr('id');
				if ($.inArray(identifiant, ['illustrations', 'portfolio', 'documents']) < 0) {
					identifiant = null;
				}

				titre.append(
					"<div class='minidoc'>"
					+ "<span class='icone grand on' title='Affichage en grand'></span>"
					+ "<span class='icone cases' title='Affichage en cases'></span>"
					+ "<span class='icone liste' title='Affichage en liste courte'></span>"
					+ "</div>"
				);

				var changer_affichage_doccuments = function(me, bouton, classe) {
					$(me).parent().find('.icone').removeClass('on').end().end().addClass('on');
					var liste = $(me).parents('h3').next('.liste_items.documents');
					liste.removeClass('documents_cases').removeClass('documents_liste');
					if (classe) {
						liste.addClass(classe);
					}
					if (identifiant) {
						$.cookie('affichage-' + identifiant, bouton);
					}
				};

				titre.find('.minidoc > .grand').click(function () {
					changer_affichage_doccuments(this, 'grand', null);
				});

				titre.find('.minidoc > .cases').click(function () {
					console.log('clic');
					changer_affichage_doccuments(this, 'cases', 'documents_cases');
				});

				titre.find('.minidoc > .liste').click(function () {
					changer_affichage_doccuments(this, 'liste', 'documents_liste');
				});

				if (identifiant) {
					var defaut = $.cookie('affichage-' + identifiant);
					if (defaut) {
						titre.find('.minidoc > .' + defaut).trigger('click');
					}
				}
			});
		}
		minidoc();
		onAjaxLoad(minidoc);
	});

})(jQuery);