// JavaScript Document

function fp_ShowImg (url_hight, url_low, largeur, hauteur, titre, descriptif) {
	var el = document.getElementById("affiche_photo");
	var lien = el.getElementsByTagName("a")[0];
	lien.href = url_hight;
	lien.title = titre;
	var photo = el.getElementsByTagName("img")[0];
	photo.visiblity = "hidden";
	photo.src = url_low;
	photo.width = largeur;
	photo.height = hauteur;
	photo.alt = titre;
	photo.visiblity = "visible";
	document.getElementById("titre_photo").innerHTML = titre;
	document.getElementById("descriptif_photo").innerHTML = descriptif;
}