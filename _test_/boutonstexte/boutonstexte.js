// on dépend de jQuery (ou autre définissant $)
if (typeof $ == 'function') {
	$(document).ready(function(){
		$("#contenu .texte").before(

'<span class="boutonstexte">\
<button class="textonly" onclick="boutonstexte.textOnly(this);" alt="' +
 boutonstexte.txtOnly + '" title="' +
 boutonstexte.txtOnly + '"><img src="' +
 boutonstexte.imgPath + '/textonly.png" /></button>\
<button class="textsizeup" onclick="boutonstexte.fontBigger(this);" alt="' +
 boutonstexte.txtSizeUp + '" title="' +
 boutonstexte.txtSizeUp + '"><img src="' +
 boutonstexte.imgPath + '/fontsizeup.png" /></button>\
<button class="textsizedown" onclick="boutonstexte.fontSmaller(this);" alt="' +
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
	var that = this;
	var texte = $(elt).parent().next();
	if (this['backTextOnly']) {
		$(elt).attr({ 'title': that.txtOnly, 'alt': that.txtOnly }).
			parent().insertBefore($("#marktextonly")).after(texte).
			next().removeClass("onlytext").
			next().remove(); // enlever "#marktextonly"
		$('body').children().show();
		this.backTextOnly = false;
		return;
	}
	texte.after('<div id="marktextonly">marktextonly</div>');
	$(elt).attr({ 'title': that.txtBackSpip, 'alt': that.txtBackSpip }).
		parent().prependTo("body").after(texte).
		next().addClass("onlytext").
		next().hide();
	this.backTextOnly = true;
}
boutonsTexte.prototype.fontBigger = function(elt)
{
	var that = this;
	$(elt).parent().next().each(function(){
		var m = $(this).css('fontSize').match(/(\d+(?:\.\d+)?)(.*)/);
		this.style.fontSize = (that.rate * parseFloat(m[1])) + m[2];
	});
}
boutonsTexte.prototype.fontSmaller = function(elt)
{
	var that = this;
	$(elt).parent().next().each(function(){
		var m = $(this).css('fontSize').match(/(\d+(?:\.\d+)?)(.*)/);
		this.style.fontSize = (parseFloat(m[1]) / that.rate) + m[2];
	});
}

function dump(elt)
{var txt=''; for (var prop in elt) {txt += prop+'='+elt[prop]+'/';} alert(txt);}
