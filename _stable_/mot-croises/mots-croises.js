// déplacement dans la grille
var sens_grille='';
// sens de l'écriture : 'rtl' ou 'ltl'
var sens_curseur = ($('html').attr('dir') == 'rtl')?'w-resize':'e-resize';

// dès que le DOM est prêt...
$(document).ready(function(){
 // vérification de la présence d'une grille						    
 if ($('table.grille').length) {
	// sens d'écriture basculé à 'h' (horizontal)
	changeDir();
	// gestion du clavier par function mykey()
	$('table.grille tr td input').bind('keypress', mykey);
	// définition du bouton droit de la souris pour changer le sens de déplacement dans la grille
	if ((jQuery.browser.safari) | (jQuery.browser.Konqueror))
		$('form.grille').bind('contextmenu', changeDir);
	else
		$('table.grille tr td input').bind('contextmenu', changeDir);	
 }
});

function changeDir(e) {
	
	if(sens_grille=='h') {
		sens_grille='v';
		style='s-resize';
	} else {
		sens_grille='h';
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
			if(sens_grille=='h') x++; else y++;
	}
	x=""+x; y=""+y;

	$('#col'+x+'lig'+y).each(function(){
		this.focus();
	});

	return true;
}