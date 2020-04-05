/*
 * ce code s'inspire largement du plugin Enluminures Typographiques
 */

// variables
var ancien_lien, arguments_balise = [	"texte", "titre", "lien", "skel", "width", "height"/*, "options"*/ ];

/*
 * Objet 'selection'
 */
function spipopup_selection(zone){
	this.sel = "";
	this.sel_before = "";
	this.sel_after = "";
	this.entrees = new Array(); // les variables du modele SPIP
	this.recup_lien = recup_lien;
	this.recup_texte = recup_texte;
	this.recup_titre = recup_titre;
	this.recup_skel = recup_skel;
	this.recup_width = recup_width;
	this.recup_height = recup_height;
	this.recup_options = recup_options;
	this.existe = existe;

	var _zoneval = (zone.value==undefined) ? '' : zone.value;

	if ((clientVer >= 4) && is_ie && is_win)
	{
		var theSelection = false;
		theSelection = top.opener.document.selection.createRange().text; // Get text selection
		if (theSelection) {
			this.sel = theSelection;
		} else {
			this.sel_before = top.opener.document.getElementById("text_area").value;
		}

	} else {
		var selLength = zone.textLength;
		var selStart = zone.selectionStart;
		var selEnd = zone.selectionEnd;
		if (selEnd == 1 || selEnd == 2) selEnd = selLength;
		// Raccourcir la selection par double-clic si dernier caractere est espace	
		if (selEnd - selStart > 0 && _zoneval.substring(selEnd-1,selEnd) == ' ') selEnd = selEnd-1;
		this.sel_before = _zoneval.substring(0,selStart);
		this.sel = _zoneval.substring(selStart, selEnd);
		this.sel_after = _zoneval.substring(selEnd, selLength);
	}

	_tmpsel = this.sel;
	_tmpsel = _tmpsel.replace(/</, ''); // '<' du debut
	_tmpsel = _tmpsel.replace(/>/, ''); // '>' de fin
	this.entrees = _tmpsel.split("|");

	function recup_titre(){
		for (var i=0; i<this.entrees.length; i++){
			if (this.entrees[i].match(/^titre=([^\|]*)/)) return this.entrees[i].match(/^titre=([^\|]*)/)[1];
		}
		return '';
	}

	function recup_texte(){
		// Si une seule entree qui n'est pas 'popup', on le prend comme texte
		if (this.entrees.length==1 && this.entrees[0]!='popup') return this.entrees[0];
		for (var i=0; i<this.entrees.length; i++){
			if (this.entrees[i].match(/^texte=([^\|]*)/)) return this.entrees[i].match(/^texte=([^\|]*)/)[1];
		}
		return '';
	}

	function recup_lien(){
		for (var i=0; i<this.entrees.length; i++){
			if (this.entrees[i].match(/^lien=([^\|]*)/)) return this.entrees[i].match(/^lien=([^\|]*)/)[1];
		}
		return '';
	}

	function recup_width(){
		for (var i=0; i<this.entrees.length; i++){
			if (this.entrees[i].match(/^width=([^\|]*)/)) return this.entrees[i].match(/^width=([^\|]*)/)[1];
		}
		return '';
	}

	function recup_height(){
		for (var i=0; i<this.entrees.length; i++){
			if (this.entrees[i].match(/^height=([^\|]*)/)) return this.entrees[i].match(/^height=([^\|]*)/)[1];
		}
		return '';
	}

	function recup_skel(){
		for (var i=0; i<this.entrees.length; i++){
			if (this.entrees[i].match(/^skel=([^\|]*)/)) return this.entrees[i].match(/^skel=([^\|]*)/)[1];
		}
		return '';
	}

	function recup_options(){
		for (var i=0; i<this.entrees.length; i++){
			if (this.entrees[i].match(/^options=([^\|]*)/)) return this.entrees[i].match(/^options=([^\|]*)/)[1];
		}
		return '';
	}

	function existe(){ 
		return ( (this.sel.length==undefined || this.sel.length<=0) ? false : true );
	}

}

/*
 * Initialisation : on recupere le code initial en zone de selection
 */	
function spipopup_init(){
	ancien_lien = new spipopup_selection(top.opener.zone_selection);
	if (ancien_lien.existe()) {
		document.getElementById("lien").value = ancien_lien.recup_lien();
		document.getElementById("texte").value = ancien_lien.recup_texte();
		document.getElementById("titre").value = ancien_lien.recup_titre();
		document.getElementById("skel").value = ancien_lien.recup_skel();
		document.getElementById("width").value = ancien_lien.recup_width();
		document.getElementById("height").value = ancien_lien.recup_height();
//		document.getElementById("options").value = ancien_lien.recup_options();
	}
}

/*
 * Generation du code de la balise
 */
function spipopup_construire_code(){
	var texte_balise = '<popup';
	for (var i=0; i<this.arguments_balise.length; i++){
		var arg_in_form = document.getElementById( arguments_balise[i] ).value;
		if (arg_in_form.length) 
			texte_balise += '|'+arguments_balise[i]+'='+arg_in_form;
	}
	return texte_balise+'>';
}

/*
 * Verifie les saisies obligatoires
 */
function spipopup_verifier_form(){
	var _ret, 
			lien_str = document.getElementById("lien").value,
			texte_str = document.getElementById("texte").value;

	_ret = true;
	document.getElementById("lien_erreur").innerHTML = '';
	document.getElementById("texte_erreur").innerHTML = '';

	if (lien_str.length==0 || texte_str.length==0) {
		_ret = false;
		if (lien_str.length==0) {
			document.getElementById("lien_erreur").innerHTML = POPUPFORM_ERR_LIEN==undefined ? 'necessaire' : POPUPFORM_ERR_LIEN;
		}
		if (texte_str.length==0) {
			document.getElementById("texte_erreur").innerHTML = POPUPFORM_ERR_TEXTE==undefined ? 'necessaire' : POPUPFORM_ERR_TEXTE;
		}
	}

	return _ret;
}
	
/*
 * Enregsitrement, verficiation, construction puis renvoie sur l'opener
 */
function spipopup_enregistrer(){
	if (spipopup_verifier_form()) {
		nouveau_lien = spipopup_construire_code();
		if (ancien_lien.existe()) {
			if ((clientVer >= 4) && is_ie && is_win) {
				top.opener.document.selection.createRange().text = nouveau_lien;
			} else {
				top.opener.zone_selection.value = ancien_lien.sel_before + nouveau_lien + ancien_lien.sel_after;
			}
		} else { //insertion d'un nouveau tableau
			// on ajoute un espace a la fin
			nouveau_lien += ' ';
			if (top.opener.zone_selection.createTextRange && top.opener.zone_selection.caretPos) { //IE
				var caretPos = top.opener.zone_selection.caretPos;
				caretPos.text = caretPos.text + nouveau_lien;
				top.opener.zone_selection.focus();
			} else {
				top.opener.zone_selection.value = ancien_lien.sel_before + nouveau_lien + ancien_lien.sel_after;
			}
		}
		window.close();
	}
}
