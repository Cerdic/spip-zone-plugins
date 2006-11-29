// déplacement dans la grille
var sens_grille='';
// sens de l'écriture : 'rtl' ou 'ltl'
var sens_curseur = ($('html').attr('dir') == 'rtl')?'w-resize':'e-resize';

// dès que le DOM est prêt...
$(document).ready(function(){
 // vérification de la présence d'une grille						    
 if ($('table.grille').length) {
	// sens d'écriture basculé à 'h' (horizontal)
	changeDeDirection();
	// gestion du clavier par function clavierPourJeux()
	$('table.grille tr td input').bind('keypress', clavierPourJeux);
	// définition du bouton droit de la souris pour changer le sens de déplacement dans la grille
	if ((jQuery.browser.safari) | (jQuery.browser.Konqueror))
		$('form.grille').bind('contextmenu', changeDeDirection);
	else
		$('table.grille tr td input').bind('contextmenu', changeDeDirection);	
 }
});

function changeDeDirection(e) {
	
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

function clavierPourJeux(e) {
	//var key = e.keyCode ? e.keyCode : (e.which ? e.which: 0);
	var m = this.name.match(/GR(\d+)x(\d+)/);
	var x=m[1];
	var y=m[2];
	var retour = true;
	
	switch(e.keyCode) {
		case 40: y++; break; 
		case 38: y--; break;
		case 36: y=1; x=1; break;
		case 37: if (sens_curseur=='e-resize') x--; else x++; break;
		case 39: if (sens_curseur=='e-resize') x++; else x--; break;
		case 8:; case 46: this.value = ''; break;
		case 0 : if (e.which) { this.value = String.fromCharCode(e.which).toUpperCase(); retour = false }
		default:
			if(sens_grille=='h') x++; else y++;
			// Pour IE ...
			if ((e.which==null) && (e.keyCode>=46)) {
				this.value = String.fromCharCode(e.keyCode).toUpperCase();
				retour = false;
			}
	}

	var newcell = '#GR'+String(x)+'x'+String(y);
	$(newcell).each(function(){ this.focus(); });
	
	return retour;
}