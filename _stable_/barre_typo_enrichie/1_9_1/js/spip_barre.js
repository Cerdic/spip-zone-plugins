// Barre de raccourcis
// derive du:
// bbCode control by subBlue design : www.subBlue.com

// Startup variables
var theSelection = false;

// Variables globales
var currentTimeout;

// D�clencher une fonction apr�s n secondes ou annuler un appel pr�c�dent � la fonction
function delayFunction(callbackFunction, seconds){
	if(this.currentTimeout)
		clearTimeout(this.currentTimeout);
	if(callbackFunction && seconds)
		this.currentTimeout = setTimeout(callbackFunction, seconds*1000);
}

function toggle_preview() {
	if ($("#article_preview").css("display") == "none") {
		$("#text_area").css("height",parseInt($("#text_area").css("height"))/2+"px");
		$("#article_preview").css("height",$("#text_area").css("height"));
		$("#article_preview").show();
		MajPreview();
	} else {
		$("#text_area").css("height",parseInt($("#text_area").css("height"))*2+"px");
		$("#article_preview").hide();
	}
}

function preview_off() {
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


function barre_raccourci(debut,fin,champ) {
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
		mozWrap(txtarea, debut, fin);
		return;
	}
}

function barre_demande(debut,milieu,fin,affich,bulle,champ) {
	var inserer = affich;
	var monhelp ="";
	if (bulle != "") {monhelp = "|"+bulle; }

	if (inserer != null) {
		if (inserer == "") {inserer = "xxx"; }

		barre_raccourci(debut, monhelp+milieu+inserer+fin, champ);
	}
}

function barre_demande_lien(debut,milieu,fin,affich,bulle,langue,champ) {
	if (langue != "") {bulle = bulle+"{"+langue+"}"; }
	barre_demande(debut,milieu,fin,affich,bulle,champ);
}

function barre_ancre(debut,milieu,fin,affich,champ) {
	var inserer = affich;
	var renvoi = '';
	if (inserer != null) {
		if (inserer == "") {inserer = "xxx"; }
		barre_raccourci(debut+inserer+milieu+fin, renvoi, champ);
	}
}

function barre_inserer(text,champ) {
	var txtarea = champ;
	
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	} else {
		//txtarea.value  += text;
		//txtarea.focus();
		mozWrap(txtarea, '', text);
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

function barre_searchreplace(chercher,remplacer, rec_tout, rec_case, rec_entier, champ) {
	
	var condition = "";
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
/*   mozWrap(txtarea, debut, fin); */
}

function barre_2Majuscules(champ) {
	var oldSelStart = champ.selectionStart;
	var oldSelEnd = champ.selectionEnd;
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
	champ.focus();
}

function barre_2Minuscules(champ) {
	var oldSelStart = champ.selectionStart;
	var oldSelEnd = champ.selectionEnd;
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
	champ.focus();
}

// D'apres Nicolas Hoizey 
function barre_tableau(toolbarfield,cols,rows,tete,caption)
{
	var txtarea = toolbarfield;
	txtarea.focus();
//	var cols = 2;
//	var rows = 2;
//	var tete = 1;

if (cols != null && rows != null) {
		var tbl = '';
		var ligne = '|';
		var captiontxt = '|| titre | resum\351 ||';
		var entete = '|';
		var marqueur =' |';
		
		for(i = 0; i < cols; i++) {
			ligne = ligne + ' valeur |';
			entete = entete + ' {{entete}}' + marqueur;
		}
		for (i = 0; i < rows; i++) {
			tbl = tbl + ligne + '\n';
		}
		if (tete==true) {
			tbl = entete + '\n' + tbl;
		}
		if (caption==true) {
			tbl = captiontxt + '\n' + tbl;
		}
		if ((clientVer >= 4) && is_ie && is_win) {
			var str = document.selection.createRange().text;
			var sel = document.selection.createRange();
			sel.text = str + '\n\n' + tbl + '\n\n';
		} else {
			mozWrap(txtarea, '', "\n\n" + tbl + "\n\n");
		}
	}
	return;
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
function mozWrap(txtarea, open, close)
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
	MajPreview();
	return;
}

// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
     function storeCaret (textEl) {
       if (textEl.createTextRange) 
         textEl.caretPos = document.selection.createRange().duplicate();
     }

