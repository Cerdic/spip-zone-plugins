// on dépend de jQuery (ou autre définissant $)
if (typeof $ == 'function') {
	$(document).ready(function(){
		$("#contenu .texte").prepend(

'<span class="boutonstexte">\
<button onclick="boutonstexte.textOnly(this);" alt="' +
 boutonstexte.txtOnly + '" title="' +
 boutonstexte.txtOnly + '"><img src="' +
 boutonstexte.imgPath + '/textonly.png" /></button>\
<button onclick="boutonstexte.fontBigger(this);" alt="' +
 boutonstexte.txtSizeUp + '" title="' +
 boutonstexte.txtSizeUp + '"><img src="' +
 boutonstexte.imgPath + '/fontsizeup.png" /></button>\
<button onclick="boutonstexte.fontSmaller(this);" alt="' +
 boutonstexte.txtSizeDown + '" title="' +
 boutonstexte.txtSizeDown + '"><img src="' +
 boutonstexte.imgPath + '/fontsizedown.png"/></button></span>'

		);
	});
} else {
	alert('btc a besoin de jQuery !');
}

// le prototype boutons du contenu
function boutonsTexte(options)
{
	this.rate = 1.2;
    for (opt in options) {
        this[opt] = options[opt];
    }
}
boutonsTexte.prototype.textOnly = function(elt)
{
	if (elt['backTextOnly']) {
		elt['backTextOnly'].show();
		elt['backTextOnly'].prev().remove();
		return;
	}
	elt['backTextOnly'] = $(elt).ancestors(".texte").prependTo("body").next();
	elt['backTextOnly'].hide();
}
boutonsTexte.prototype.fontBigger = function(elt)
{
	var that = this;
	$(elt).ancestors(".texte").each(function(){
		var m = $(this).css('fontSize').match(/(\d+(?:\.\d+)?)(.*)/);
		this.style.fontSize = (that.rate * parseFloat(m[1])) + m[2];
	});
}
boutonsTexte.prototype.fontSmaller = function(elt)
{
	var that = this;
	$(elt).ancestors(".texte").each(function(){
		var m = $(this).css('fontSize').match(/(\d+(?:\.\d+)?)(.*)/);
		this.style.fontSize = (parseFloat(m[1]) / that.rate) + m[2];
	});
}

function dump(elt)
{var txt=''; for (var prop in elt) {txt += prop+'='+elt[prop]+'/';} alert(txt);}
