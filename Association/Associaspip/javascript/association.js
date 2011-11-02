function afficheDiv(nomDiv){
	_modifie_div(nomDiv,'')
}

function cacheDiv(nomDiv){
	_modifie_div(nomDiv,'cachediv');
}

function _modifie_div(nomDiv,val) {
	var nomDiv = 'saisie_' + nomDiv;
	if(document.getElementById && document.getElementById(nomDiv)) { // Pour les navigateurs récents
		ptrDiv = document.getElementById(nomDiv); modif = true;
	}
	else if(document.all && document.all[nomDiv]) { // Pour les veilles versions
		ptrDiv = document.all[nomDiv]; modif = true;
	}
	else if(document.layers && document.layers[nomDiv]) { // Pour les très veilles versions
		ptrDiv = document.layers[nomDiv]; modif = true;
	}
	else {
		modif = false;
	}
	if(modif) {
		ptrDiv.className = val ;
	}
}

function remplirSelectImputation(numClasse, numImputation) {
	var i = 0;
	var numSelect = 0;
	var myselect = document.getElementById("imputation");
	for (var code in eval('classe'+numClasse)) {
		leCode = code;
		if (leCode == numImputation) {numSelect = i;}
		laValeur = code + ' - ' + eval('classe'+numClasse)[code];
		lOption = new Option(laValeur, leCode, [], []);
		myselect.options[i] = lOption;
		i++;
	}
	myselect.selectedIndex = numSelect;
	myselect.length = i;
}

function chkForm(frm) {
	for (var i=1; i<frm.arguments.length; i++){
		fld=frm.arguments[i];
		i++;
		txt=frm.arguments[i];
		if(document.forms[frm].elements[fld].value == "") {
			alert(txt);
			document.forms[frm].elements[fld].focus();
			return false;
		}
	}
	if(!isCheckMailOk(document.getElementById("email").value))	{
		alert('Veuillez saisir une adresse électronique valide.');
		document.getElementById("email").focus();
		return false;
	}
	return true;
}

function isCheckMailOk(e) {
	if(e==null || e.length==0) return false;
	ok = "1234567890qwertyuiopasdfghjklzxcvbnm.@-_QWERTYUIOPASDFGHJKLZXCVBNM";
	for(i=0; i < e.length ;i++)	{
		if(ok.indexOf(e.charAt(i))<0) {return false;}
	}
	if(document.images) {
		re = "/(@.*@)|(\.\.)|(^\.)|(^@)|(@$)|(\.$)|(@\.)/";
		re_two = "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/";
		if(!e.match(re) && e.match(re_two)) {return true;}
		return false
	}
	return true;
}