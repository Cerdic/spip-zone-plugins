// code commun avec le sudoku
if(sens_grille==undefined) {

	// deplacement dans la grille
	var sens_grille = '';
	// sens de l'ecriture : 'rtl' ou 'ltl'
	var sens_curseur = (jQuery('html').attr('dir') == 'rtl')?'w-resize':'e-resize';
	// selecteur sur les cases
	var selecteur_case = 'table.jeux_grille tr td input';
	
	// des que le DOM est pret...
	jQuery(document).ready(function(){
	 // verification de la presence d'une grille	
	 if (jQuery('table.jeux_grille').length) {
		jQuery(selecteur_case).hover(function(){
		  jQuery(this).css("background-color", "aliceblue");
		}, function(){
		  jQuery(this).css("background-color", "");
		});			 
		// sens d'ecriture bascule a 'h' (horizontal)
		changeDeDirection();
		// gestion du clavier par function clavierPourJeux()
		jQuery(selecteur_case).bind('keyup', clavierPourJeux);
		// definition du bouton droit de la souris pour changer le sens de deplacement dans la grille
		if ((jQuery.browser.safari) | (jQuery.browser.Konqueror))
			jQuery('form.jeux_grille').bind('contextmenu', changeDeDirection);
		else
			jQuery(selecteur_case).bind('contextmenu', changeDeDirection);	
	 }
	});
	
	function changeDeDirection(e) {
		if(sens_grille == 'h') {
			sens_grille = 'v';
			style = 's-resize';
		} else {
			sens_grille = 'h';
			style = sens_curseur;
		}
		jQuery(selecteur_case).css('cursor', style);
		return false;
	}
	
	function clavierPourJeux(e) {
		// var key = e.keyCode ? e.keyCode : (e.which ? e.which: 0);
		// format : reponsesXXXXX-C1-L1
		var m = this.id.match(/(.*?)\-C(\d+)\-L(\d+)/);
		if (m === null) return false;
		var x = m[2];
		var y = m[3];
		var retour = true;
	
		switch(e.keyCode) {
			case 46: this.value = ''; break;
			case 40: y++; break; 
			case 38: y--; break;
			case 36: y = 1; x = 1; break;
			case 37: if (sens_curseur == 'e-resize') x--; else x++; break;
			case 39: if (sens_curseur == 'e-resize') x++; else x--; break;
			case 0 : if (e.which) { this.value = String.fromCharCode(e.which).toUpperCase(); retour = false }
			case 8: if(sens_grille == 'h') x--; else y--; break; 
			default:
				if(sens_grille == 'h') x++; else y++;
				// Pour IE ...
				// if ((e.which==null) && (e.keyCode>=46)) {
				if(jQuery.browser.msie && (e.keyCode>=46)) {
					this.value = String.fromCharCode(e.keyCode).toUpperCase();
					retour = false;
				}
		}
	
		var newcell = '#' + m[1] + '-C' + String(x) + '-L' + String(y);
		jQuery(newcell).each(function(){ this.focus(); });
		
		return retour;
	}

}