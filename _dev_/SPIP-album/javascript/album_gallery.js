// JavaScript Document

function Affiche_Image (url, src, largeur, hauteur, titre, descriptif) {
	var el = document.getElementById("affiche_photo");
	var lien = el.getElementsByTagName("a")[0];
	lien.href = url;
	lien.title = titre;
	lien.visiblity = "hidden";
	document.getElementById("grande_photo").innerHTML = '<img src="'+src+'" alt="'+titre+'" title="Cliquez sur la photo pour la visualiser dans sa taille originale." width="'+largeur+'" height="'+hauteur+'"  style="height:'+hauteur+'px;width:'+largeur+'px;" />';
	lien.visiblity = "visible";
	document.getElementById("titre_photo").innerHTML = titre;
	document.getElementById("descriptif_photo").innerHTML = descriptif;
}