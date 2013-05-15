jQuery.fn.repairLink2Anchor = function() {
	if ($("base").length) {// Le tag <base..> existe
		var href = '',
			anchor = '',
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

jQuery.fn.hasAttr = function(name) {  
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
		$("div.encart").html($("div.encart").attr("rel")).attr("rel","").repairLink2Anchor();
		$("h3 a").each(function(){
			if($(this).hasAttr('name') && $(this).parents('h3').find('a.tdm').size()==0)
				$(this).parents('h3').append(tdm_retour);
		});
	}
}

var tdm_flottante_init = function(){
	if(typeof(tdm_flottante) != 'undefined' && tdm_flottante && $("#tdm").size() == 1){
		var titres_offset = [],
			tdm = $("#tdm");
		
		tdm.parents("div:not(.encart)").eq(0).find('h3').each(function(i, titre) {
			titres_offset.push($(titre).offset().top.toFixed()-100);
		});
		jQuery(window).bind('scroll',function() {
			var offset = tdm.parents("div:not(.encart)").eq(0).offset();
			if(offset){
				var limite_tdm = offset.top-20,
					limite_bas = limite_tdm+tdm.parents("div:not(.encart)").eq(0).height(),
					pos_bas = tdm.offset().top+tdm.height(),
					top = $(window).scrollTop();
				if((top >= limite_tdm) && (pos_bas <= limite_bas) && (top < limite_bas)){
					/**
					 * Initialisation si première fois 
					 */
					if(!tdm.hasClass("tdm_flottant")){
						$('h3 a.tdm').hide();
						tdm.addClass("tdm_flottant");
						var tdm_width = tdm.width();
						tdm.css({"position": "fixed", "top": 0, "width": tdm_width+"px","z-index":"999"});
						tdm.find('h2').addClass('active');
						tdm.find('.active,h2').css({'cursor':'pointer'}).unbind('click').click(function(){
							tdm.find('li:not(.active)').toggle();
							return false;
						});
					}
					var highlighted =false;
					for (var i = 0, c = titres_offset.length; i < c; i++) {
						$('li,h2', tdm).removeClass('active');
						if (titres_offset[i] >= top) {
							if((i-1) == -1)
								highlighted = $('h2', tdm).addClass('active');
							else
								highlighted = $('li:eq('+(i-1)+')', tdm).addClass('active');
							break;
						}
					}
					if(!highlighted)
						highlighted = $('li:eq('+(titres_offset.length-1)+')', tdm).addClass('active');
					tdm.find('li:not(.active),h2:not(.active)').hide();
					tdm.find('.active').show();
						jQuery('#tdm .active a').unbind('click').click(function(e){
							if(jQuery('#tdm li:not(:visible)').size() > 0){
								e.preventDefault();
								jQuery('#tdm *:not(:visible)').show();
								return false
							}
						});
					}
				if(($(window).scrollTop() < limite_tdm)||(pos_bas > limite_bas) ){
					$('h3 a.tdm').show();
					tdm.find('*:hidden').show();
					tdm.find('.active,h2').css({'cursor':'inherit'}).unbind('click');
					tdm.removeClass("tdm_flottant").css({"position": "static", "width": "auto"});
				}
			}
		});
	};
}
jQuery(document).ready(function(){
	tdm_init();
	tdm_flottante_init();
	onAjaxLoad(tdm_init);
	onAjaxLoad(tdm_flottante_init);
});