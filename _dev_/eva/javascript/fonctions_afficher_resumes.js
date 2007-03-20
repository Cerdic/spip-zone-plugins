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
		valeur_message_resumes = "Masquer";
	}
	else {
		valeur2 = "none";
		valeur_message_resumes = "Afficher"
	}

	if (clic == "clic"){
		if(valeur=="block"){
			valeur="none";
			valeur2="none";
			valeur_message_resumes="Afficher"
		}
		else if(valeur=="none") {
			valeur="block";
			valeur2="block";
			valeur_message_resumes="Masquer";
		}
		
	}
if(document.all){
// Pour Internet Explorer
	//alert(valeur);
	for (var k=0; k<document.all["introduction_opt_"+bloc].length; k++) {
	document.all["introduction_opt_"+bloc][k].style.display = valeur;
	}
// Ce qui suit ne peut pas, pour IE6, etre fait n'importe quand :
// la page doit etre entierement chargee pour qu'innerHtml opere
// sur son parent cf. <http://support.microsoft.com/?scid=kb%3Ben-us%3B276228&x=8&y=8>
  	//for (var l=0; l<document.all["message_resumes_"+bloc].length; l++){
  	document.all["message_resumes_"+bloc].innerHTML = valeur_message_resumes;
  	//}
}

else {

// Autres navigateurs, en esperant qu'ils respectent mieux les normes
	for (var j=0; j<document.getElementsByName("introduction_opt_"+bloc).length; j++) {
		document.getElementsByName("introduction_opt_"+bloc)[j].style.display = valeur;
	}
	for (var j=0; j<document.getElementsByName("message_resumes_"+bloc).length; j++) {
	document.getElementsByName("message_resumes_"+bloc)[j].innerHTML = valeur_message_resumes;
	}
}
createCookie("affichage_resumes_"+bloc,valeur2,7);
}
