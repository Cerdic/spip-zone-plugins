var tradlang_switchers = function(){
	$('#formulaire_switcher_langue .fieldset ul,#formulaire_switcher_langue .boutons,div.tradlang_choisir_module:not(".module_changed") .formulaire_choisir_module .fieldset ul,div.tradlang_choisir_module:not(".module_changed") .formulaire_choisir_module .boutons,table.info.trads tbody').hide();
	$('#formulaire_switcher_langue .fieldset h3,.formulaire_choisir_module .fieldset h3,table.info.trads caption').each(function(){
		if($(this).find('a.legend_link').size() == 0){
			$(this).wrapInner('<a href="#" class="legend_link"></a>');
			$(this).find('a.legend_link').unbind('click').click(function(){
				if($(this).parent('h3').next('ul').is(':hidden') || $(this).parent('caption').next('tbody').is(':hidden'))
					$(this).parents('.formulaire_spip,table').find('.fieldset ul:hidden,.boutons:hidden,tbody:hidden').slideDown();
				else
					$(this).parents('.formulaire_spip,table').find('.fieldset ul,.boutons,tbody').slideUp();
				return false;
			});
		}
	});
}

var tradlang_thead_flottant = function(){
	if($(".bilan table.spip").not('.flotting').length > 0){
		$("table.spip").not('.flotting').each(function(){
			var table = $(this),
				thead = $(this).find('thead'),
				thead_width = $(this).width(),
				offset = thead.offset(),
				limite_thead= offset.top,
				limite_bas = limite_thead+$(this).height()-$(this).find("tfoot").height()-thead.height();
			$(window).scroll(function() {
				var pos_bas = thead.offset().top+thead.height();
				if(($(window).scrollTop() >= limite_thead) && (pos_bas <= limite_bas) && ($(window).scrollTop() < limite_bas)){
					if(!thead.hasClass("thead_flottant")){
						thead.find('th').each(function(){
							$(this).css({'width':$(this).width()+'px'});
						});
						table.find('tfoot tr').eq(0).find('td').each(function(){
							$(this).css({'width':$(this).width()+'px'});
						});
						thead.addClass("thead_flottant").css({"position": "fixed", "top": '0px', "width": thead_width+"px","z-index":"999"});
					}
				}
				if(($(window).scrollTop() < limite_thead) || (pos_bas > limite_bas)){
					thead.removeClass("thead_flottant").css({"position": "static", "width": "auto"});
					thead.find('th').css({'width':'auto'});
					table.find('tbody tr').eq(0).find('td').css({'width':'inherit'});
				}
			});
			table.addClass('flotting');
		});
	}
}

var tradlang_tabs_charger = function(){
	jQuery('#infos_auteur_tabs').tabs();
};

(function($) {
	$.fn.equalHeights = function(minHeight, maxHeight) {
		var tallest = (minHeight) ? minHeight : 0;
		this.each(function() {
			if($(this).height() > tallest)
				tallest = $(this).height();
		});
		if((maxHeight) && tallest > maxHeight) tallest = maxHeight;
		return this.each(function() {
			$(this).height(tallest).css("overflow","hidden");
		});
	}
})(jQuery);


var tradlang_hauteur_blocs = function(){
	if(typeof(jQuery.fn.equalHeights) ==  'function')
		jQuery('.traducteurs li.item').equalHeights();
}

$(document).ready(function(){
	tradlang_switchers();
	tradlang_thead_flottant();
	tradlang_hauteur_blocs();
	onAjaxLoad(tradlang_switchers);
	onAjaxLoad(tradlang_thead_flottant);
	onAjaxLoad(tradlang_tabs_charger);
	onAjaxLoad(tradlang_hauteur_blocs);
});

$(window).load(function(){
	tradlang_tabs_charger();
});