// JavaScript Document
// function pour affichage smileys dans texte sur un clic


function emoticon(text) {
	var txtarea = document.formulaire.texte;
	text = ' ' + text + ' ';
		
	if(txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	} else {
		txtarea.value += text;
		txtarea.focus();
	} 
}
