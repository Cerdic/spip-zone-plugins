// on dépend de jQuery (ou autre définissant $)
if (typeof $ == 'function') {
	$(document).ready(function(){
		$("#contenu .texte").prepend(

'<span class="boutons_contenu">\
<button onclick="boutons_contenu.textOnly(this);" alt="' +
 boutons_contenu.txtSizeUp + '" title="' +
 boutons_contenu.txtSizeUp + '"><img src="' +
 boutons_contenu.imgPath + '/textonly.png" /></button>\
<button onclick="boutons_contenu.fontBigger(this);" alt="' +
 boutons_contenu.txtSizeUp + '" title="' +
 boutons_contenu.txtSizeUp + '"><img src="' +
 boutons_contenu.imgPath + '/fontsizeup.png" /></button>\
<button onclick="boutons_contenu.fontSmaller(this);" alt="' +
 boutons_contenu.txtSizeDown + '" title="' +
 boutons_contenu.txtSizeDown + '"><img src="' +
 boutons_contenu.imgPath + '/fontsizedown.png"/></button></span>'

		);
	});
} else {
	alert('btc a besoin de jQuery !');
}

// le prototype boutons du contenu
function boutonsContenu(options)
{
    for (opt in options) {
        this[opt] = options[opt];
    }
}
boutonsContenu.prototype.textOnly = function(elt)
{
	if (elt['backTextOnly']) {
		elt['backTextOnly'].show();
		elt['backTextOnly'].prev().remove();
		return;
	}
	elt['backTextOnly'] = $(elt).ancestors(".texte").prependTo("body").next();
	elt['backTextOnly'].hide();
}
boutonsContenu.prototype.fontBigger = function(elt)
{
	$(elt).ancestors(".texte").each(function(){
		this.style.fontSize = (1.2 * parseFloat($(this).css('fontSize'))) + 'px';
	});
}
boutonsContenu.prototype.fontSmaller = function(elt)
{
	$(elt).ancestors(".texte").each(function(){
		this.style.fontSize = (parseFloat($(this).css('fontSize')) / 1.2) + 'px';
	});
}

function dump(elt)
{var txt=''; for (var prop in elt) {txt += prop+'='+elt[prop]+'/';} alert(txt);}
