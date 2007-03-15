// Les fonctions createCookie() et readCookie()
// ont ete recuperees depuis <http://www.quirksmode.org/js/cookies.html>
// dans le respect des droits de reproduction declares sur
// <http://www.quirksmode.org/about/copyright.html> (utilisation libre)
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function afficher_resume(clic){
	var valeur = readCookie("affichage_resumes");
	if(valeur=="block"){
		valeur2 = "block";
		valeur3 = "Masquer";
	}
	else {
		valeur2 = "none";
		valeur3 = "Afficher"
	}

	if (clic == "clic"){
//	alert("valeur avant clic : " + valeur)
//	alert("clic : " + clic);
		if(valeur=="block"){
			valeur="none";
			valeur2="none";
			valeur3="Afficher"
		}
		else if(valeur=="none") {
			valeur="block";
			valeur2="block";
			valeur3="Masquer";
		}
		
	}
// alert("valeur finale : " + valeur);
if(document.all){
/*	for (i=0; i<document.all("introduction_opt").length; i++) {
		document.all("introduction_opt")[i].style.display = valeur;
	}
	document.all("message_resumes")[0].innerHTML = valeur3;*/
}

else {
	for (i=0; i<document.getElementsByName("introduction_opt").length; i++) {
		/* if (document.all){document.getElementsByName[""}} */
		document.getElementsByName("introduction_opt")[i].style.display = valeur;
	}
	document.getElementsByName("message_resumes")[0].innerHTML = valeur3;
}
createCookie("affichage_resumes",valeur2,7);
}