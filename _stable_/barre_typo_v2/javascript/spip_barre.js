// Barre de raccourcis
// derive du:
// bbCode control by subBlue design : www.subBlue.com

// Startup variables
var theSelection = false;

// Variables globales
var currentTimeout;

// Ancienne function de SPIP 1.9.2 non dispo en 1.9.3
function bte_swap_couche(couche, rtl, dir, no_swap) { 
	var layer; 
	var triangle = document.getElementById('triangle' + couche); 
	if (!(layer = findObj('Layer' + couche))) return; 
	if (layer.style.display == "none"){ 
			layer.style.display = 'block'; 
	} else { 
			layer.style.display = 'none'; 
	} 
}

if(typeof swap_couche=="undefined"){
	swap_couche = bte_swap_couche;
}

// Déclencher une fonction après n secondes ou annuler un appel précédent à la fonction
function delayFunction(callbackFunction, seconds){
	if(this.currentTimeout)
		clearTimeout(this.currentTimeout);
	if(callbackFunction && seconds)
		this.currentTimeout = setTimeout(callbackFunction, seconds*1000);
}

function toggle_preview(barre, strchamp) {
	champ = eval(strchamp);
	if ($("#article_preview"+barre).css("display") == "none") {
		$(champ).css("height",parseInt($(champ).css("height"))/2+"px");
		$("#article_preview"+barre).css("height",$(champ).css("height"));
		$("#article_preview"+barre).show();
		MajPreview(barre,strchamp);
	} else {
		$(champ).css("height",parseInt($(champ).css("height"))*2+"px");
		$("#article_preview"+barre).hide();
	}
}

function toggle_stats(barre,strchamp) {
	champ = eval(strchamp);
	if ($("#article_stats"+barre).css("display") == "none") {
		$("#article_stats"+barre).show();
		MajStats(barre,strchamp);
	} else {
		$("#article_stats"+barre).hide();
	}
}

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;

var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);


function barre_raccourci(debut,fin,champ, barre) {
	var txtarea = champ;

	txtarea.focus();
	donotinsert = false;
	theSelection = false;
	bblast = 0;

	if ((clientVer >= 4) && is_ie && is_win)
	{
		theSelection = document.selection.createRange().text; // Get text selection
		if (theSelection) {

			while (theSelection.substring(theSelection.length-1, theSelection.length) == ' ')
			{
				theSelection = theSelection.substring(0, theSelection.length-1);
				fin = fin + " ";
			}
			if (theSelection.substring(0,1) == '{' && debut.substring(0,1) == '{')
			{
				debut = debut + " ";
			}
			if (theSelection.substring(theSelection.length-1, theSelection.length) == '}' && fin.substring(0,1) == '}')
			{
				fin = " " + fin;
			}

			// Add tags around selection
			document.selection.createRange().text = debut + theSelection + fin;
			txtarea.focus();
			theSelection = '';
			return;
		}
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, debut, fin, barre);
		return;
	}
}

function barre_demande(debut,milieu,fin,affich,bulle,champ,barre) {
	var inserer = affich;
	var monhelp ="";
	if (bulle != "") {monhelp = "|"+bulle; }

	if (inserer != null) {
		if (inserer == "") {inserer = "xxx"; }

		barre_raccourci(debut, monhelp+milieu+inserer+fin, champ, barre);
	}
}

function barre_demande_lien(debut,milieu,fin,affich,bulle,langue,champ,barre) {
	if (langue != "") {bulle = bulle+"{"+langue+"}"; }
	barre_demande(debut,milieu,fin,affich,bulle,champ,barre);
}

function barre_ancre(debut,milieu,fin,affich,champ,barre) {
	var inserer = affich;
	var renvoi = '';
	if (inserer != null) {
		if (inserer == "") {inserer = "xxx"; }
		barre_raccourci(debut+inserer+milieu+fin, renvoi, champ, barre);
	}
}

function barre_inserer(text,champ, barre) {
	var txtarea = champ;
	
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	} else {
		//txtarea.value  += text;
		//txtarea.focus();
		mozWrap(txtarea, '', text, barre);
		return;
	}
}

// Attention : rec_tout (mot entier) n'est pas actif !
function barre_search(chercher,rec_entier, rec_case, champ) {
	if(chercher != null && champ.value != null) {
		if(champ.selectionStart == champ.selectionEnd) {
			ndx = 0;
		} else {
			ndx = champ.selectionEnd;
		}
		if (rec_case == false) {
			var x = champ.value.toLowerCase().indexOf(chercher.toLowerCase(),ndx);
		} else {
			var x = champ.value.indexOf(chercher,ndx);
		}
		if(x!=-1)
		{ 
			var end = (x+chercher.length);
			champ.setSelectionRange(x,end);
			champ.focus();
		}
	}
}

function barre_searchreplace(chercher,remplacer, rec_tout, rec_case, rec_entier, champ, barre) {
	var condition = "";
	var selTop = champ.scrollTop;
// les parametres (casse + global)
	if (rec_tout == true) {
		condition = condition + "g";
	} 
	if (rec_case == false) {
		condition = condition + "i";
	} 
	if (rec_entier == true) {
		chercher = chercher + " ";
		remplacer = remplacer + " ";
	} 
	var re = new RegExp(chercher, condition);
	champ.value = champ.value.replace(re, remplacer);
	champ.scrollTop = selTop;
	champ.focus();
	MajPreview(barre, champ.id);
}


function barre_capitales(champ,majuscules,barre) {
	var txtarea = champ;

	txtarea.focus();

	if (majuscules!=true) {
		majuscules=false;
	}

	if ((clientVer >= 4) && is_ie && is_win)
	{
		theSelection = document.selection.createRange().text; // Get text selection
		if (theSelection) {
			if (majuscules) {
				document.selection.createRange().text = theSelection.toUpperCase();
			} else {
				document.selection.createRange().text = theSelection.toLowerCase();
			}
			txtarea.focus();
			theSelection = '';
			MajPreview(barre, txtarea.id);
			return;
		}
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		if (majuscules) {
			barre_2Majuscules(champ, barre);
		} else {
			barre_2Minuscules(champ, barre);
		}
		return;
	}
}

function barre_2Majuscules(champ, barre) {
	var oldSelStart = champ.selectionStart;
	var oldSelEnd = champ.selectionEnd;
	var selTop = champ.scrollTop;
	if(oldSelStart == oldSelEnd) {
		champ.value = champ.value.toUpperCase();
	} else {
		var val = champ.value.substring(champ.selectionStart,champ.selectionEnd);
		var oldSelStart = champ.selectionStart;
			
		val = val.toUpperCase();
				
		champ.value = champ.value.substring(0,champ.selectionStart)
		+val
		+champ.value.substring(champ.selectionEnd,champ.value.length);
	}
	champ.setSelectionRange(oldSelStart,oldSelEnd);
	champ.scrollTop = selTop;
	champ.focus();
	MajPreview(barre, champ.id);
}

function barre_2Minuscules(champ, barre) {
	var oldSelStart = champ.selectionStart;
	var oldSelEnd = champ.selectionEnd;
	var selTop = champ.scrollTop;
	if(oldSelStart == oldSelEnd) {
		champ.value = champ.value.toLowerCase();
	} else {
		var val = champ.value.substring(champ.selectionStart,champ.selectionEnd);
		var oldSelStart = champ.selectionStart;
			
		val = val.toLowerCase();
				
		champ.value = champ.value.substring(0,champ.selectionStart)
		+val
		+champ.value.substring(champ.selectionEnd,champ.value.length);
	}
	champ.setSelectionRange(oldSelStart,oldSelEnd);
	champ.scrollTop = selTop;
	champ.focus();
	MajPreview(barre, champ.id);
}




// Shows the help messages in the helpline window
function helpline(help, champ) {
	champ.value = help;
}


function setCaretToEnd (input) {
	setSelectionRange(input, input.value.length, input.value.length);
}


function setSelectionRange(input, selectionStart, selectionEnd) {
	if (input.setSelectionRange) {
		input.focus();
		input.setSelectionRange(selectionStart, selectionEnd);
	}
	else if (input.createTextRange) {
		var range = input.createTextRange();
		range.collapse(true);
		range.moveEnd('character', selectionEnd);
		range.moveStart('character', selectionStart);
		range.select();
	}
}

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close, barre)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	if (selEnd == 1 || selEnd == 2)
		selEnd = selLength;
	var selTop = txtarea.scrollTop;

	// Raccourcir la selection par double-clic si dernier caractere est espace	
	if (selEnd - selStart > 0 && (txtarea.value).substring(selEnd-1,selEnd) == ' ') selEnd = selEnd-1;
	
	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);

	// Eviter melange bold-italic-intertitre
	if ((txtarea.value).substring(selEnd,selEnd+1) == '}' && close.substring(0,1) == "}") close = close + " ";
	if ((txtarea.value).substring(selEnd-1,selEnd) == '}' && close.substring(0,1) == "}") close = " " + close;
	if ((txtarea.value).substring(selStart-1,selStart) == '{' && open.substring(0,1) == "{") open = " " + open;
	if ((txtarea.value).substring(selStart,selStart+1) == '{' && open.substring(0,1) == "{") open = open + " ";

	txtarea.value = s1 + open + s2 + close + s3;
	selDeb = selStart + open.length;
	selFin = selEnd + close.length;
	window.setSelectionRange(txtarea, selDeb, selFin);
	txtarea.scrollTop = selTop;
	txtarea.focus();
	MajPreview(barre, txtarea.id);
	return;
}

// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret (textEl) {
	if (textEl.createTextRange) 
		textEl.caretPos = document.selection.createRange().duplicate();
}

//insere un tableau courcy michael ec49.org/sitenkit2/
var zone_selection;
function barre_tableau(champs_de_texte, cheminediteur){
	zone_selection = champs_de_texte;
	hauteur=600;
	largeur=700;
	propriete='scrollbars=yes,resizable=yes,width='+largeur+',height='+hauteur;
	w=window.open(cheminediteur+'?exec=tableau_edit', '',propriete);
}

// DEB Galerie JPK 
// idée originale de http://www.gasteroprod.com/la-galerie-spip-pour-reutiliser-facilement-les-images-et-documents.html
function barre_galerie(champs_de_texte, cheminediteur) {
	zone_selection = champs_de_texte;
	window.open(cheminediteur+'?exec=galerie', 'galerie',
		'width=550,height=400,menubar=no,scrollbars=yes,statusbar=yes')
}

function barre_selection(champ) {
	var resultat = "";
	champ.focus();
	if (champ.setSelectionRange)
		resultat = $(champ).text().substring(champ.selectionStart, champ.selectionEnd);
	else if ((clientVer >= 4) && is_ie && is_win) {
		resultat = document.selection.createRange().text;
	}

	if (resultat.length==0) {
		resultat = $(champ).val();
	}
	return resultat;
}

function barre_content(champ) {
	var resultat = "";
	champ.focus();
	resultat = $(champ).val();
	return resultat;
}

function MajPreview(num_barre,champ) {
	if ($("#article_preview"+num_barre).css("display") != "none" && $("#article_stats'"+num_barre).css("display") != "none") {
		delayFunction("MajCallBack("+num_barre+","+String(champ)+")",1);
	} else {
		if ($("#article_preview"+num_barre).css("display") != "none") {
			delayFunction("MajPreviewCallBack("+num_barre+","+String(champ)+")",1);
		}
		if ($("#article_stats"+num_barre).css("display") != "none") {
			delayFunction("MajStatsCallBack("+num_barre+","+String(champ)+")",1);
		}
	}
}

function MajStats(num_barre,champ) {
	if ($("#article_stats"+num_barre).css("display") != "none") {
		delayFunction("MajStatsCallBack("+num_barre+","+String(champ)+")",1);
	}
}

function MajPreviewCallBack(num_barre,champ) {
	$.post("?exec=article_preview", { texte:barre_content(champ) }, function(data) {
		$("#article_preview"+num_barre).empty()
		$("#article_preview"+num_barre).append(data);
		});
}

function MajStatsCallBack(num_barre,champ) {
	$.post("?exec=article_stats", { texte:barre_selection(champ) }, function(data) {
		$("#article_stats"+num_barre).empty()
		$("#article_stats"+num_barre).append(data);
		});
}

function MajCallBack(num_barre,champ) {
	MajStatsCallBack(num_barre,champ);
	MajPreviewCallBack(num_barre,champ);
}
