// code commun avec les mots-croises
if(sens_grille==undefined) {

	// deplacement dans la grille
	var sens_grille='';
	// sens de l'ecriture : 'rtl' ou 'ltl'
	var sens_curseur = (jQuery('html').attr('dir') == 'rtl')?'w-resize':'e-resize';
	
	// des que le DOM est pret...
	jQuery(document).ready(function(){
	 // verification de la presence d'une grille	
	 if (jQuery('table.jeux_grille').length) {
		// sens d'ecriture bascule a 'h' (horizontal)
		changeDeDirection();
		// gestion du clavier par function clavierPourJeux()
		$('table.jeux_grille tr td input').bind('keypress', clavierPourJeux);
		// definition du bouton droit de la souris pour changer le sens de deplacement dans la grille
		if ((jQuery.browser.safari) | (jQuery.browser.Konqueror))
			jQuery('form.jeux_grille').bind('contextmenu', changeDeDirection);
		else
			jQuery('table.jeux_grille tr td input').bind('contextmenu', changeDeDirection);	
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
		$('table.jeux_grille tr td input').css('cursor', style);
		return false;
	}
	
	function clavierPourJeux(e) {
		//var key = e.keyCode ? e.keyCode : (e.which ? e.which: 0);
		var m = this.name.match(/GR(\d+)x(\d+)x(\d+)/);
		var x=m[2];
		var y=m[3];
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
				// if ((e.which==null) && (e.keyCode>=46)) {
				if(jQuery.browser.msie && (e.keyCode>=46)) {
					this.value = String.fromCharCode(e.keyCode).toUpperCase();
					retour = false;
				}
		}
	
		var newcell = '#GR'+m[1]+'x'+String(x)+'x'+String(y);
		jQuery(newcell).each(function(){ this.focus(); });
		
		return retour;
	}

}