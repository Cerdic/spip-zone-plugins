// dans quelle direction on ecrit (h ou v)
var sens='h';
if ($('html').attr('dir') == 'rtl') 
	var sens_curseur='w-resize';
else
	var sens_curseur='e-resize';
$(document).ready(function(){
	$('table.grille tr td input').bind('keypress', mykey).attr('style','cursor:'. sens_curseur);
	if ((jQuery.browser.safari) | (jQuery.browser.Konqueror))
		$('form.grille').bind('contextmenu', changeDir);
	else
		$('table.grille tr td input').bind('contextmenu', changeDir);	
});

function changeDir(e) {
	
	if(sens=='h') {
		sens='v';
		style='s-resize';
	} else {
		sens='h';
		style=sens_curseur;
	}
	$('table.grille tr td input').css('cursor', style);
	return false;
}

function mykey(e) {
	var m = this.name.match(/col(\d+)lig(\d+)/);
	
	var x=m[1];
	var y=m[2];
	
	switch(e.keyCode) {
		case 40: y++; break; 
		case 38: y--; break;
		case 37: if (sens_curseur=='e-resize') x--; else x++; break;
		case 39: if (sens_curseur=='e-resize') x++; else x--; break;
		case 8 : break;
		default:
			if(sens=='h') x++; else y++;
	}
	x=""+x; y=""+y;

	$('#col'+x+'lig'+y).each(function(){
		this.focus();
	});

	return true;
}