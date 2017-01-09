(function($) {
	$(document).ready(function(){
		var minidoc = function() {
			$('#portfolios h3:not(:has(.minidoc))').each(function () {
				var titre = $(this);
				var liste = titre.next('.liste_items.documents');

				titre.append(
					"<div class='minidoc'>"
					+ "<span class='icone grand on' title='Affichage en grand'></span>"
					+ "<span class='icone cases' title='Affichage en cases'></span>"
					+ "<span class='icone liste' title='Affichage en liste courte'></span>"
					+ "</div>"
				);

				titre.find('.minidoc > .grand').click(function () {
					$(this).parent().find('.icone').removeClass('on').end().end().addClass('on');
					var liste = $(this).parents('h3').next('.liste_items.documents');
					liste.removeClass('documents_cases').removeClass('documents_liste');
				});

				titre.find('.minidoc > .liste').click(function () {
					$(this).parent().find('.icone').removeClass('on').end().end().addClass('on');
					var liste = $(this).parents('h3').next('.liste_items.documents');
					liste.removeClass('documents_cases').addClass('documents_liste');
				});

				titre.find('.minidoc > .cases').click(function () {
					$(this).parent().find('.icone').removeClass('on').end().end().addClass('on');
					var liste = $(this).parents('h3').next('.liste_items.documents');
					liste.removeClass('documents_liste').addClass('documents_cases');
				});
			});
		}
		minidoc();
		onAjaxLoad(minidoc);
	});

})(jQuery);