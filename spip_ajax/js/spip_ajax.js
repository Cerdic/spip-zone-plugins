/* Fonctions generiques gerant des fonction ajax dans l'admin de spip */
spip_ajax = {};

// objet javascript servant a envoyer les requetes
spip_ajax.req = {};

// On va récupérer le hash_env pour traiter la validite
// de l'operation
spip_ajax.hash_env="";
spip_ajax.get_hash_env = function(){
	spip_ajax.req = {hash_env : "hash_env" , callback : 'spip_ajax.set_hash_env()'	}
	spip_ajax.ajax();
}

spip_ajax.set_hash_env = function(){
	spip_ajax.hash_env = spip_ajax.retour;
}


// valeur retourner par la requete ajax, cette valeur 
// est utilisable dans les fonctions de call-back
spip_ajax.retour = "";

// tableau contenant les parametres de l'url
spip_ajax.getter = new Array();

// fonction qui recupere les parametres de l'url
spip_ajax.set_get = function(){
    param = window.location.search.slice(1,window.location.search.length);
    first = param.split("&");
    for(i=0;i<first.length;i++){
        second = first[i].split("=");
        val = second[0];
        spip_ajax.getter[val] = second[1];
    }
}

// recuperation des parametres de l'url
spip_ajax.set_get();

// recuperation d'un parametre particulier de l'url
spip_ajax.get = function (nom){ 
	return spip_ajax.getter[nom];
}

// fonction envoyant les requetes ajax
// en cas de succes on peux realiser certaines actions
spip_ajax.ajax = function (){
	if (spip_ajax.hash_env!="") spip_ajax.req.hash_env = spip_ajax.hash_env;

	$.ajax({
	   type: "POST",
	   url : "?exec=_spip_ajax",
	   data: spip_ajax.req,
	   success: function(x){
	       if (spip_ajax.req.alert) alert(spip_ajax.req.alert + " \n" + x);

	       if (spip_ajax.req.refresh) $(spip_ajax.req.refresh).html(x);

	       if (spip_ajax.req.append) $(spip_ajax.req.append).append(x);


	       if (spip_ajax.req.callback){
	       	 spip_ajax.retour = x;
	       	 eval(spip_ajax.req.callback);
	       }

	       // on verifie que le code n'a rien renvoye
	       // si c'est le cas on affiche le message
	       if (spip_ajax.req.verif_succes) {
	       		x = $.trim(x);
	       		x=='' ? alert(spip_ajax.req.verif_succes) : alert(x);
	       }

	   },
	   error : function(){
	   	  alert("probleme");
	   }

 	});
}

// On appelle la fonction pour recupere le hash
spip_ajax.get_hash_env();


/* Ensemble des fonctionnalites pour spip ajax Pro*/
/* Pour les equivalences php voir le site http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_in_array/ */
/*  equivalent de la fonction php in_array*/
spip_ajax.in_array = function(array,val){
	for(i = 0 ;  i < array.length ; i++) if(array[i] == val)return true;
    return false;
}


