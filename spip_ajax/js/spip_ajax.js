/* Fonctions generiques gerant des fonction ajax dans l'admin de spip */

var spip_ajax = {
		
		// objet javascript servant a envoyer les requetes
		req : {},
		
		// On va récupérer le hash_env pour traiter la validite des requetes ajax
		hash_env : "",
		
		set_hash_env : function(){
			$sa.hash_env = $sa.retour;
			for(i=0;i<$sa.array_onload.length;i++)eval($sa.array_onload[i]);
		
		},
		
		// valeur retourner par la requete ajax, cette valeur 
		// est utilisable dans les fonctions de call-back
		retour : "",
		
		
		// tableau contenant les parametres de l'url
		getter : new Array(),
		
		
		// fonction qui recupere les parametres de l'url
		set_get : function(){
		    param = window.location.search.slice(1,window.location.search.length);
		    first = param.split("&");
		    for(i=0;i<first.length;i++){
		        second = first[i].split("=");
		        val = second[0];
		        $sa.getter[val] = second[1];
		    }
		},
		
		
		// recuperation d'un parametre particulier de l'url
		get : function (nom){ 
			return $sa.getter[nom];
		},
		
		// liste des fonctions  a appele une fois le hash env recuperer
		array_onload : new Array(),
		onload : function(fonc){
			$sa.array_onload.push(fonc+"()");
			// gere le cas de IE ....
			if ($sa.hash_env !="") eval(fonc+"()");
		},
	
		
		// fonction envoyant les requetes ajax
		// en cas de succes on peux realiser certaines actions
		ajax : function (obj){
			
			// on test si l'objet servant a la requete est present et correct
			if (typeof obj=='object') $sa.req = obj;
			
			// on force le data type a html avant de tester si un autre type est demande
			dataType = 'html';
			if ($sa.req.dataType) dataType = $sa.req.dataType;
						
			if ($sa.hash_env!="") $sa.req.hash_env = $sa.hash_env;

			$.ajax({
			   type: "POST",
			   url : "?exec=_spip_ajax",
			   data: $sa.req,
			   dataType : dataType,
			   success: function(x){
			       if ($sa.req.alert) alert($sa.req.alert + " \n" + x);

			       if ($sa.req.refresh) $($sa.req.refresh).html(x);

			       if ($sa.req.append) $($sa.req.append).append(x);


			       if ($sa.req.callback){
			    	   $sa.retour = x;
			       	 eval($sa.req.callback);
			       }

			       // on verifie que le code n'a rien renvoye
			       // si c'est le cas on affiche le message
			       if ($sa.req.verif_succes) {
			       		x = $.trim(x);
			       		x!=1 ?  alert('Votre action n\'a pas renvoy\351e \'true\'') : alert($sa.req.verif_succes) ;
			       }

			   },
			   error : function(e){
			   	  alert("une erreur est survenu dans votre requete ajax - Spip Ajax");
			   }

		 	});
		},
		
		in_array : function(array,val){
			for(i = 0 ;  i < array.length ; i++) if(array[i] == val)return true;
		    return false;
		}

		
	
};

// creation d'un alias pour spip_ajax
var $sa= spip_ajax;

// recuperation des parametres de l'url
$sa.set_get();

// et on appelle la fonction pour recupere le hash
$sa.ajax({hash_env : "hash_env" , callback : 'spip_ajax.set_hash_env()'	});

