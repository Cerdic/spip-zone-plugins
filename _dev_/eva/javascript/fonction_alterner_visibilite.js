var liste_cache = new Array(0);
var liste_cache_secteurs = new Array (0);

function alterner_visibilite(liste, onload){
	var el  = document.getElementById(liste).getElementsByTagName("LI");
	if(onload == "non"){

		if(document.images['puce_'+liste].src.match("folder_blue.png")){
			document.images['puce_'+liste].src = "/plugins/eva/images/folder_blue_open.png"
		}
		else{
			document.images['puce_'+liste].src = "/plugins/eva/images/folder_blue.png"
		}
	}

	for(i=0; i<el.length; i++){
		if(el[i].style.display=="none"){
			el[i].style.display="block";
		}
		else{
			el[i].style.display="none";
		}
	}
}

function tout_ouvrir(){
	var el  = document.getElementsByTagName("LI");
	for(var g=0; g<el.length; g++){
			el[g].style.display="block";
			el[g].style.backgroundColor="#ffffff";
			el[g].style.fontSize="15px";
	}
	document.getElementById("Contenu").style.width="80%";
	document.getElementById("Contenu").style.left="10%";
	
}

var valeur_pop = "";
function afficher_populaires(){
	if(valeur_pop==""){
		document.getElementById('populaires_menu').style.display='block';
		valeur_pop="masquer";
	}
	else{
		document.getElementById('populaires_menu').style.display='none';
		valeur_pop="";
	}
}