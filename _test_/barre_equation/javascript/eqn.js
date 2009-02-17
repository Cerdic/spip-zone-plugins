// surcharge di mozrap per la correzione dellla lunghezza per le stringhe latex con slashes e backslashes

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

	// mmmx
	if ( (close.substring(0,1) == '\\')||(close.substring(1,2) == '/') ) {		
		selEnd = selEnd -1;
	}
	txtarea.value = s1 + open + s2 + close + s3;
	selDeb = selStart + open.length;
	selFin = selEnd + close.length;
	window.setSelectionRange(txtarea, selDeb, selFin);
	txtarea.scrollTop = selTop;
	txtarea.focus();
	MajPreviewCallBack(barre, txtarea);
	return;
}