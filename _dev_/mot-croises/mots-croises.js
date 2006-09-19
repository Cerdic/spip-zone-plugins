// dans quelle direction on ecrit (h ou v)
var sens='h';

$(document).ready(function(){
	$('table.grille tr td input').css('cursor', 'e-resize').bind('contextmenu', changeDir).bind('keypress', mykey);
	
	
});

function changeDir(e) {
	
	if(sens=='h') {
		sens='v';
		style='s-resize';
	} else {
		sens='h';
		style='e-resize';
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
		case 37: x--; break;
		case 39: x++; break;
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