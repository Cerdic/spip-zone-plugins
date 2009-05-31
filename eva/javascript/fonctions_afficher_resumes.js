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

function afficher_resume(clic, bloc){
// Cette fonction permet d'alternet l'affichage du resume des articles, breves et
// sites dans la page sommaire.html et de retenir son etat dans un cookie

	var valeur = readCookie("affichage_resumes_"+bloc);
	if(!valeur){
	// Pour IE6, une valeur nulle ne vaut pas none, il faut donc declarer cette valeur
	// explicitement.
		valeur = "none";
		}
	if(valeur=="block"){
		valeur2 = "block";
		valeur_message_resumes = "Masquer les r&eacute;sum&eacute;s";
	}
	else {
		valeur2 = "none";
		valeur_message_resumes = "Afficher les r&eacute;sum&eacute;s"
	}

	if (clic == "clic"){
		if(valeur=="block"){
			valeur="none";
			valeur2="none";
			valeur_message_resumes="Afficher les r&eacute;sum&eacute;s"
		}
		else if(valeur=="none") {
			valeur="block";
			valeur2="block";
			valeur_message_resumes="Masquer les r&eacute;sum&eacute;s";
		}
		
	}
	var compteur_introduction = 1;
	while(document.getElementById("introduction_opt_"+bloc+"_"+compteur_introduction)){
		document.getElementById("introduction_opt_"+bloc+"_"+compteur_introduction).style.display = valeur;
		compteur_introduction++;
	}

	var compteur_introduction_logo = 1;
	while(document.getElementById("introduction_opt_logo_"+bloc+"_"+compteur_introduction_logo)){
		document.getElementById("introduction_opt_logo_"+bloc+"_"+compteur_introduction_logo).style.display = valeur;
		compteur_introduction_logo ++;
	}
	document.getElementById("message_resumes_"+bloc).innerHTML = valeur_message_resumes;
createCookie("affichage_resumes_"+bloc,valeur2,7);
}
