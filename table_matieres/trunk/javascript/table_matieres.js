(function($){
	
$.fn.repairLink2Anchor = function() {
	if ($("base").length) {// Le tag <base..> existe
		var href = anchor = '',
			re1 = /#([\w-]+)/, // anchor parts
			re2 = /^#([\w-]+)/, // only anchor
			thisUrl = document.location.href;// read current url
		
		thisUrl=thisUrl.replace(re1,"");// remove anchor from current url

		// loop all A tags whith attribute href
		$(this).find("a[href]").each(function(){
			href = $(this).attr("href");
			if (re2.test(href)) {// check - href is anchor?
				anchor = href.match(re2);
				$(this).attr("href", thisUrl + '#' + anchor[1]);
			}
		});
	}
}

$.fn.hasAttr = function(name) {  
	return this.attr(name) !== undefined;
};

/**
 * Initialisation de la table des matières
 * 
 * Si on a un encart avec un rel
 * 
 * Le rel devient le contenu de l'encart
 * Chaque h3 dans le texte contenant une ancre se voit suivi de l'icone de retour à la 
 * table des matières
 */
var tdm_init = function(){
	if($("div.encart").size() >= 1 && $("div.encart").hasAttr('rel')){
		$("div.encart").html($("div.encart").attr("rel")).repairLink2Anchor();
		$("h3 a").each(function(){
			if($(this).hasAttr('name') && $(this).parents('h3').find('a.tdm').size()==0)
				$(this).parents('h3').append(tdm_retour);
		});
	}
}

var tdm_flottante_init = function(){
	if(typeof(tdm_flottante) != 'undefined' && tdm_flottante && $("#tdm").size() == 1){
		var titres_offset = limite_bas = limite_tdm = false, tdm = $("#tdm");
		
		var tdm_scroll = function(){
			var offset = tdm.parents("div:not(.encart)").eq(0).offset(), tdm_size = tdm.height();
			if(offset){
				var limite_tdm = offset.top,
					limite_bas = limite_tdm+tdm.parents("div:not(.encart)").eq(0).height()+100,
					pos_bas = tdm.offset().top+tdm.height(),
					top = $(window).scrollTop();
				if((top >= limite_tdm) && (pos_bas <= limite_bas) && (top < limite_bas)){
					if(tdm.is(':hidden')) tdm.show();
					/**
					 * Initialisation si première fois 
					 */
					if(!tdm.hasClass("tdm_flottant")){
						$('h3 a.tdm').hide();
						tdm.addClass("tdm_flottant");
						var tdm_width = tdm.width();
						tdm.css({"position": "fixed", "top": 0, "width": tdm_width+"px","z-index":"999"});
						$('h2',tdm).addClass('active');
						$('li a',tdm).css({'display':'block'});
						$('.active,h2',tdm).css({'cursor':'pointer'}).unbind('click').click(function(){
							$('li:not(.active)',tdm).toggle();
							return false;
						});
					}
					if(!titres_offset){
						titres_offset = [],
						tdm.parents("div:not(.encart)").eq(0).find('h3').each(function(i, titre) {
							titres_offset.push($(titre).offset().top.toFixed());
						});
					}
					
					var highlighted = false;
					for (var i = 0, c = titres_offset.length; i < c; i++) {
						$('li,h2', tdm).removeClass('active');
						if (titres_offset[i]-tdm.height() >= top) {
							if((i-1) == -1) highlighted = $('h2', tdm).addClass('active');
							else highlighted = $('li:eq('+(i-1)+')', tdm).addClass('active');
							break;
						}
					}
					if(!highlighted)
						highlighted = $('li:eq('+(titres_offset.length-1)+')', tdm).addClass('active');
					$('li:not(.active),h2:not(.active)',tdm).hide();
					$('.active',tdm).show();
					var tdm_active_click = function(e){
						if($('li:not(:visible)',tdm).size() > 0){
							e.preventDefault();
							e.stopPropagation();
							$('*:not(:visible)',tdm).show();
							return false;
						}
					}
					$('.active a',tdm).unbind('click').click(tdm_active_click);
				}
				if(($(window).scrollTop() < limite_tdm) && tdm.hasClass("tdm_flottant")){
					$('*:hidden',tdm).add('h3 a.tdm').show();
					$('.active,h2',tdm).css({'cursor':''}).removeClass('active').unbind('click');
					$('li a',tdm).css({'display':''});
					tdm.removeClass("tdm_flottant").css({'position': '', 'width':'','z-index':'','top':''});
					if(typeof(ancre_douce_init) == 'function')
						ancre_douce_init();
				}else if((pos_bas > limite_bas) && tdm.is(':visible'))
					tdm.hide();
			}
		}
		$(window).unbind('scroll',tdm_scroll).bind('scroll',tdm_scroll);
		if($(window).scrollTop() > 0)
			tdm_scroll();
	};
}
$(function(){
	tdm_init();
	tdm_flottante_init();
	onAjaxLoad(tdm_init);
	onAjaxLoad(tdm_flottante_init);
});
})(jQuery);