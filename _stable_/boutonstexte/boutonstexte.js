/*
 *  boutonstexte.js (c) toggg http://toggg.com 2006 -- licence LGPL
 */

// on dépend de jQuery
if (typeof jQuery == 'function') {
	jQuery(document).ready(function(){
		boutonstexte.tmp = '';
		boutonstexte.fixedUp = jQuery("img.textsizeup");
		if (boutonstexte.fixedUp.length) {
			boutonstexte.fixedUp
			  .click(function() {boutonstexte.fontBigger();})
			  .attr({'alt':boutonstexte.txtSizeUp, 'title':boutonstexte.txtSizeUp});
		} else if (boutonstexte.txtSizeUp) {
			boutonstexte.tmp +=
			  '<button class="textsizeup"' +
			  'onclick="boutonstexte.fontBigger(this);" alt="' +
			  boutonstexte.txtSizeUp + '" title="' +
			  boutonstexte.txtSizeUp + '"><img src="' +
			  boutonstexte.imgPath + '/fontsizeup.png" /></button>';
		}
		boutonstexte.fixedDown = jQuery("img.textsizedown");
		if (boutonstexte.fixedDown.length) {
			boutonstexte.fixedDown
			  .click(function() {boutonstexte.fontSmaller();})
			  .attr({'alt':boutonstexte.txtSizeDown, 'title':boutonstexte.txtSizeDown});
		} else if (boutonstexte.txtSizeDown) {
			boutonstexte.tmp +=
			  '<button class="textsizedown"' +
			  'onclick="boutonstexte.fontSmaller(this);" alt="' +
			  boutonstexte.txtSizeDown + '" title="' +
			  boutonstexte.txtSizeDown + '"><img src="' +
			  boutonstexte.imgPath + '/fontsizedown.png" /></button>';
		}
		if (boutonstexte.txtOnly) {
			boutonstexte.tmp +=
			  '<button class="textonly"' +
			  'onclick="boutonstexte.textOnly(this);" alt="' +
			  boutonstexte.txtOnly + '" title="' +
			  boutonstexte.txtOnly + '"><img src="' +
			  boutonstexte.imgPath + '/textonly.png" /></button>';
		}
		if (boutonstexte.tmp) {
			jQuery(boutonstexte.selector).before(
				'<span class="boutonstexte">' + boutonstexte.tmp + '</span>');
		}
	});
} else {
	alert('boutonstexte a besoin de jQuery !');
}

// le prototype boutons du contenu
function boutonsTexte(options)
{
	this.rate = 1.2;
	this.selector = "#contenu .texte";
    for (opt in options) {
        this[opt] = options[opt];
    }
}
boutonsTexte.prototype.textOnly = function(elt)
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
