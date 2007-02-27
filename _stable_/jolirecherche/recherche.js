/*
 * www.merzok.com
 * jolie saisie de recherche
 * Copyleft - all rights reversed.
 *
 */
	if (document.images) {
		var rerienim =new Image();  rerienim.src ="plugins/jolirecherche/formulaires/rerien.gif"; 
		var reok =new Image();  reok.src ="plugins/jolirecherche/formulaires/reok.gif"; 
	}
	
	function tac_fo() {
		document.images['rerien'].src = rerienim.src;	
		document.re.recherche.value = "";
		document.re.recherche.focus();
	}
	
	function tac_ok() {	
		if (document.re.recherche.value > "") {
			if (document.re.recherche.value == "recherche") { return; }
			document.images['rerien'].src = reok.src;
		}
		else {
			document.images['rerien'].src = rerienim.src;
		}
	}