// on dépend de jQuery (ou autre définissant $)
if (typeof $ == 'function') {
	$(document).ready(function(){
		$("#contenu .texte").prepend(

'<span class="boutons_contenu">\
<button onclick="boutons_contenu.fontBigger(this);"><img src="' +
 boutons_contenu.imgPath + '/fontBigger.png" /></button>\
<button onclick="boutons_contenu.fontSmaller(this);"><img src="' +
 boutons_contenu.imgPath + '/fontSmaller.png" /></button></span>'

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
boutonsContenu.prototype.fontBigger = function(elt)
{
	$(elt).ancestors(".texte").each(function(){
		this.style.fontSize = (1.2 * parseInt($(this).css('fontSize'))) + 'px';
	});
}
boutonsContenu.prototype.fontSmaller = function(elt)
{
	$(elt).ancestors(".texte").each(function(){
		this.style.fontSize = (parseInt($(this).css('fontSize')) / 1.2) + 'px';
	});
}

function dump(elt)
{var txt=''; for (var prop in elt) {txt += prop+'='+elt[prop]+'/';} alert(txt);}
