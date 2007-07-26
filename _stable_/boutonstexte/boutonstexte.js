/*
 *  boutonstexte.js (c) toggg http://toggg.com 2006 -- licence LGPL
 */

// on dépend de jQuery
if (typeof jQuery == 'function')
(function($){
	$(function(){

		var boutons = $('<span class="boutonstexte"></span>');

		if (!$("img.textsizeup").length && boutonstexte.txtSizeUp) {
			$('<button class="textsizeup"' +
			  '"><img src="' + boutonstexte.imgPath +
			  '/fontsizeup.png" /></button>')
			.appendTo(boutons);
		}
		if (!$("img.textsizedown").length && boutonstexte.txtSizeDown) {
			$('<button class="textsizedown"' +
			  '"><img src="' + boutonstexte.imgPath +
			  '/fontsizedown.png" /></button>')
			.appendTo(boutons);
		}
		if (!$("img.textonly").length && boutonstexte.txtOnly) {
			$('<button class="textonly"' +
			  '"><img src="' + boutonstexte.imgPath +
			  '/textonly.png" /></button>')
			.appendTo(boutons);
		}

		if (boutons.html()) {
			$(boutonstexte.selector).before(boutons);
		}

		$("img.textsizeup,button.textsizeup")
		.click(function(e) {
			boutonstexte.fontBigger($(this).is('button') ? this : null);
			e.stopPropagation();
		})
		.attr({'alt':boutonstexte.txtSizeUp, 'title':boutonstexte.txtSizeUp});

		$("img.textsizedown,button.textsizedown")
		.click(function(e) {
			boutonstexte.fontSmaller($(this).is('button') ? this : null);
			e.stopPropagation();
		})
		.attr({'alt':boutonstexte.txtSizeDown, 'title':boutonstexte.txtSizeDown});

		$("img.textonly,button.textonly")
		.click(function(e) {
			boutonstexte.texteOnly($(this).is('button') ? this : null);
			e.stopPropagation();
		})
		.attr({'alt':boutonstexte.txtOnly, 'title':boutonstexte.txtOnly});

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
		jQuery(elt).attr({ 'title': that.txtOnly, 'alt': that.txtOnly }).
			parent().insertBefore(jQuery("#marktextonly")).after(texte);
		jQuery("#marktextonly").remove();
		jQuery('body').children().show();
		this.backTextOnly = false;
		return;
	}
	texte.addClass("onlytext");
	jQuery(texte[texte.length-1]).after('<div id="marktextonly">marktextonly</div>');
	jQuery(elt).attr({ 'title': that.txtBackSpip, 'alt': that.txtBackSpip }).
		parent().prependTo("body").after(texte).
		siblings().gt(texte.length-1).hide();
	this.backTextOnly = true;
}
boutonsTexte.prototype.fontBigger = function(elt)
{
	var that = this;
	var work = elt ? jQuery(elt).parent().next() : jQuery(this.selector);
	work.each(function(){
		var m = jQuery(this).css('fontSize').match(/(\d+(?:\.\d+)?)(.*)/);
		this.style.fontSize = (that.rate * parseFloat(m[1])) + m[2];
	});
}
boutonsTexte.prototype.fontSmaller = function(elt)
{
	var that = this;
	var work = elt ? jQuery(elt).parent().next() : jQuery(this.selector);
	work.each(function(){
		var m = jQuery(this).css('fontSize').match(/(\d+(?:\.\d+)?)(.*)/);
		this.style.fontSize = (parseFloat(m[1]) / that.rate) + m[2];
	});
}

function dump(elt)
{var txt=''; for (var prop in elt) {txt += prop+'='+elt[prop]+'/';} alert(txt);}
