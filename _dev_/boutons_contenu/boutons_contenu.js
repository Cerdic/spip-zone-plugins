// on dépend de jQuery (ou autre définissant $)
if (typeof $ == 'function') {
	$(document).ready(function(){
		$("#contenu .titre").append(

'<span class="boutons_contenu">\
<a href="javascript:boutons_contenu.fontBigger();"><img src="' +
 boutons_contenu.imgPath + '/fontBigger.png" /></a>\
<a href="javascript:boutons_contenu.fontSmaller();"><img src="' +
 boutons_contenu.imgPath + '/fontSmaller.png" /></a></span>'

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
boutonsContenu.prototype.fontBigger = function()
{
	$("#contenu .texte").each(function(){
		this.style.fontSize = (1.2 * parseInt($(this).css('fontSize'))) + 'px';
	});
}
boutonsContenu.prototype.fontSmaller = function()
{
	$("#contenu .texte").each(function(){
		this.style.fontSize = (parseInt($(this).css('fontSize')) / 1.2) + 'px';
	});
}
