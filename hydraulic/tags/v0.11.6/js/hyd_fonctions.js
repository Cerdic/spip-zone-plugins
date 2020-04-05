function getSelectValue(selectId) {

	var selectElmt = document.getElementById(selectId);
	return selectElmt.options[selectElmt.selectedIndex].value;
}

function getRadioValue(radioId) {
	var radioElmt = document.getElementsByName(radioId);
	var choix = "";
	for (i=0; i<radioElmt.length; i++){
		if (radioElmt[i].checked){
			choix = radioElmt[i].value;
		}
	}
	return choix;
}