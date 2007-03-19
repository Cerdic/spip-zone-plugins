var liste_cache = new Array(0);


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
