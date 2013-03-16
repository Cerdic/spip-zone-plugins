// Fonctions pour la barre de boutons de boutons d'administration supplementaires.
// Les fonctions createCookie() et readCookie()
// ont ete recuperees depuis <http://www.quirksmode.org/js/cookies.html>
// dans le respect des droits de reproduction declares sur
// <http://www.quirksmode.org/about/copyright.html> (utilisation libre)
// Les autres fonctions ont ete creees par Vincent Ramos.

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

function masquer_boutons(valeur){
	maBarre_boutons=document.getElementById("boutons_admin_supp");
	monBouton_montrer=document.getElementById("bouton_montrer");

	if(valeur=="hidden"){
		if (document.all) { // Rustine pour IE6 -- la peste soit de ce navigateur
		document.all.boutons_admin_supp.style.visibility="hidden";
		document.all.bouton_montrer.style.visibility="visible";
		}
		else {
		maBarre_boutons.style.visibility = "hidden";
		monBouton_montrer.style.visibility = "visible";
		}
	createCookie("etat_des_boutons","hidden",7);
	}
	else if (valeur=="visible"){
		if (document.all) { // Rustine pour IE6 -- la peste soit de ce navigateur
		document.all.boutons_admin_supp.style.visibility="visible";
		document.all.bouton_montrer.style.visibility="hidden";
		}
		else {
		maBarre_boutons.style.visibility = "visible";
		monBouton_montrer.style.visibility = "hidden";
		}
	createCookie("etat_des_boutons","visible",7);
	}
}

var etat = readCookie("etat_des_boutons");
if(!etat){
	etat = "visible";
}
masquer_boutons(etat);

// =============================================
// Bouton de fixation de la barre
// N'est pas active avec IE car cela ne fonctionne pas sous IE6
// (position:fixed n'etant pas supporte).
// Amelioration possible : si IE7 comprend cette position,
// activer ce bouton pour ce navigateur.
// =============================================

var statut_fixe = readCookie("statut_fixe");

if (statut_fixe!="fixe" && statut_fixe!="pas_fixe"){
	statut_fixe="pas_fixe";
	fixer_barre(statut_fixe);
	var stop_la = "oui";
}

if(stop_la != "oui"){
	barre_boutons=document.getElementById("boutons_admin_supp");
	if (statut_fixe=="pas_fixe" && !document.all){
		barre_boutons.style.position="fixed";
		document.images['montrer1'].style.position="fixed";
		document.images['statut_fixation'].src=localisation_fixe;
		document.images['statut_fixation'].title="Ne pas garder au premier plan";
	}
	else if (statut_fixe=="fixe" && !document.all){
		document.images['statut_fixation'].src=localisation_pas_fixe;
	}
}

function swap_fixer_barre(){
	statut_fixe = readCookie("statut_fixe");
	fixer_barre(statut_fixe);
}

function fixer_barre(valeur){
	barre_boutons=document.getElementById("boutons_admin_supp");
	if(valeur=="fixe" && !document.all){
		barre_boutons.style.position="fixed";
		document.images['montrer1'].style.position="fixed";
		document.images['statut_fixation'].src=localisation_fixe;
		document.images['statut_fixation'].title="Ne pas garder au premier plan";
		createCookie("statut_fixe","pas_fixe",7);
	}
	else if (valeur=="pas_fixe" && !document.all){
		barre_boutons.style.position="absolute";
		document.images['statut_fixation'].src=localisation_pas_fixe;
		document.images['statut_fixation'].title="Garder au premier plan";
		document.images['montrer1'].style.position="absolute";
		createCookie("statut_fixe","fixe",7);
	}
	else {
		document.images['statut_fixation'].src=localisation_rien;
	}
}
function quitter(url_quitter){
	if(confirm('Voulez-vous quitter la session ?')){
		window.location.href=url_quitter;
	}
}