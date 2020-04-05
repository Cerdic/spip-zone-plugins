/*
 *  boutonstexte.js (c) toggg http://toggg.com 2006 -- licence LGPL
 */

// on dépend de jQuery
if (typeof jQuery == 'function')
(function($){
	$(function(){
		var boutonstexte = new boutonsTexte(boutonstexte_options);

		var boutons = $('<span class="boutonstexte"></span>');

		if (!$(".textsizeup").length && boutonstexte.txtSizeUp) {
			$('<a href="#" class="textsizeup auto"><img src="'+boutonstexte.imgPath+'fontsizeup.png" alt="' + boutonstexte.txtSizeUp +'" /></a>')
			.appendTo(boutons);
		}
		if (!$(".textsizedown").length && boutonstexte.txtSizeDown) {
			$('<a href="#" class="textsizedown auto"><img src="'+boutonstexte.imgPath+'fontsizedown.png" alt="' + boutonstexte.txtSizeDown +'" /></a>')
			.appendTo(boutons);
		}
		if (!$(".textonly").length && boutonstexte.txtOnly) {
			$('<a href="#" class="textonly auto"><img src="'+boutonstexte.imgPath+'textonly.png" alt="' + boutonstexte.txtOnly +'" /></a>')
			.appendTo(boutons);
		}

		if (boutons.html()) {
			$(boutonstexte.selector).before(boutons);
		}

		$("img.textsizeup,a.textsizeup,button.textsizeup")
		.click(function(e) {
			boutonstexte.fontBigger($(this).is('.auto') ? this : null);
			e.stopPropagation();
			return false;
		})
		.attr({'title':boutonstexte.txtSizeUp});

		$("img.textsizedown,a.textsizedown,button.textsizedown")
		.click(function(e) {
			boutonstexte.fontSmaller($(this).is('.auto') ? this : null);
			e.stopPropagation();
			return false;
		})
		.attr({'title':boutonstexte.txtSizeDown});

		$("img.textonly,a.textonly,button.textonly")
		.click(function(e) {
			boutonstexte.texteOnly($(this).is('.auto') ? this : null);
			e.stopPropagation();
			return false;
		})
		.attr({'title':boutonstexte.txtOnly});

	});
})(jQuery);

// le prototype boutons du contenu
function boutonsTexte(options)
{
	this.rate = 1.2;
	this.selector = "#contenu .texte";
    for (opt in options) {
        this[opt] = options[opt];
    }
}
boutonsTexte.prototype.texteOnly = function(elt)
{
	var that = this;
	var texte = jQuery(elt).parent().next();
	jQuery("body").toggleClass('onlytext_wrapper');
	if (this['backTextOnly']) {
		texte.removeClass("onlytext");
		jQuery(elt).attr({ 'title': that.txtOnly }).
			parent().insertBefore(jQuery("#marktextonly")).after(texte);
		jQuery("#marktextonly").remove();
		jQuery('body').children().removeClass('onlytext_hide');
		this.backTextOnly = false;
		return;
	}
	texte.addClass("onlytext");
	jQuery(texte[texte.length-1]).after('<div id="marktextonly">marktextonly</div>');
	$('body>*').addClass('onlytext_hide');
	jQuery(elt).attr({ 'title': that.txtBackSpip }).
		parent().prependTo("body").after(texte);
	this.backTextOnly = true;
}
boutonsTexte.prototype.fontBigger = function(elt)
{
	var that = this;
	var work = elt ? jQuery(elt).parent().next() : jQuery(this.selector);
	work.each(function(){
		// avec IE : 
		// passer par un wrapper dans le .texte car le texte peut avoir
		// un font-size:small qu'on ne peut multiplier brutalement
		wrap = jQuery(this);
		if (jQuery.browser.msie) {
			var wrap=jQuery(this).children('.fontwrap');
			if (!wrap.length) {
				jQuery(this).html("<span class='fontwrap' style='font-size:100%'>"
					+jQuery(this).html()+"</span>");
				var wrap=jQuery(this).children('.fontwrap');
			}
		}
		var m = wrap.css('fontSize').match(/(\d+(?:\.\d+)?)(.*)/);
		wrap.css('fontSize', (that.rate * parseFloat(m[1])) + m[2]);
	});
}
boutonsTexte.prototype.fontSmaller = function(elt)
{
	var that = this;
	var work = elt ? jQuery(elt).parent().next() : jQuery(this.selector);
	work.each(function(){
		// avec IE : 
		// passer par un wrapper dans le .texte car le texte peut avoir
		// un font-size:small qu'on ne peut multiplier brutalement
		wrap = jQuery(this);
		if (jQuery.browser.msie) {
			var wrap=jQuery(this).children('.fontwrap');
			if (!wrap.length) {
				jQuery(this).html("<span class='fontwrap' style='font-size:100%'>"
					+jQuery(this).html()+"</span>");
				var wrap=jQuery(this).children('.fontwrap');
			}
		}
		var m = wrap.css('fontSize').match(/(\d+(?:\.\d+)?)(.*)/);
		wrap.css('fontSize' , (parseFloat(m[1]) / that.rate) + m[2]);
	});
}

function dump(elt)
{var txt=''; for (var prop in elt) {txt += prop+'='+elt[prop]+'/';} alert(txt);}
