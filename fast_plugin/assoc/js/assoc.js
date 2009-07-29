

/*  le parametre classe correspond a la class appelle   */  
function class_assoc( classe){
	this.classe = classe;
	class_assoc.actif = true;
}

/* Variable de la classe class_assoc
 * class_assoc.req -> tableau pour l'envoie des requetes ajax
 */ 
 
class_assoc.req = {};
class_assoc.actif = false;
class_assoc.preselect = 0;
class_assoc.pagination = 0;
class_assoc.debut = "1990-01-01";
class_assoc.fin = "";
class_assoc.type = "";
class_assoc.preselect_id; 
class_assoc.type_id;
class_assoc.id;

/* On modifie la date de fin dynamiquement */
class_assoc.date_evaluate = function (){
	demain = new Date();
	demain.setTime(demain.getTime() + 24 * 3600 * 1000);
	class_assoc.fin = demain.getFullYear()+"-"+(demain.getMonth()+1)+"-"+demain.getDate();
}

class_assoc.date_evaluate();

/* On voit si l'objet actif correspond a la classe 
 * on test si l'objet a ete charge
 * et enfin on realise l'action demande
 */
class_assoc.create = function(nom,action){
	//  si aucun objet a ete charge
	// on cree un objet et on passe 
	// execute l'action
	
	
	// Parti a rechargement automqtique
	
	assoc_object = new class_assoc(nom);
	class_assoc.req = {classe : nom , method : "new" };
	class_assoc.ajax();
	
	/*
	if (!class_assoc.actif){
		assoc_object = new class_assoc(nom);
		class_assoc.req = {classe : nom , method : "new" };
		class_assoc.ajax();
	}else{
		// il s'agit de la meme classe 
		// on realise l'action
		if ( assoc_object.classe == nom ) {
				eval ("class_assoc."+ action + "()")
		}else{
			// on modifie le parametre actif
			// s'il s'agit d'un nouvel objet
			// en relodant le contenu du panel 
			// et en le rendant visible
			assoc_object.classe = nom;
			class_assoc.req = {classe : nom , method : "new" };
			class_assoc.ajax();
		}
	}*/
}


// fonction qui va activer certaine des 
// fonctions de base comme le changement de menu deroulant
class_assoc.relink = function(){
	
	// On supprime les actions existantes

	$("#assoc_panel_conteneur").unbind("draggable");
	
	
	// on met la pagination a 0
	class_assoc.pagination = 0;
	
	// On reactive les fonctions
	$("#rubrique_parent").change( function() { 
		a =$("#rubrique_parent option:selected").val();
		$("#rubrique_enfant option:gt(1)").css("display","none");
		$("#rubrique_enfant option:eq(0)").attr("selected","selected");
		// si 'choisissez' selectionne les enfants so
		if(a=="") return;
		$("#rubrique_enfant").find("[parent="+a+"]").css("display","block");
	});


	$('#date_debut_assoc').datepicker({  changeMonth: true, changeYear: true });
	$('#date_fin_assoc').datepicker({  changeMonth: true, changeYear: true });
	$("#ui-datepicker-div").css("z-index","500000");
	
	
	$("#assoc_panel_conteneur").draggable();
}

// on affiche le panneau d'assoication
class_assoc.open = function(){
	$("#assoc_panel_conteneur").css("display","block");
}

// on masque le panneau d'assoication
class_assoc.close = function(){
	$("#assoc_panel_conteneur").css("display","none");
}

class_assoc.ajax = function (){
	$.ajax({
      type: "POST", 
	  data: class_assoc.req , 
	  url: '/spip.php?page=assoc_ajax',
      success: function(x){
	  	
			// si on a un nouveau panneau ...
			// il est temps de tout enlever et tout 
			// remettre
	  		if (class_assoc.req.method == "new"){
	  		

	  				$('#date_debut_assoc').datepicker("destroy");
					$('#date_fin_assoc').datepicker("destroy");
	  	

				$("#style_assoc_panel").remove();
				$("#assoc_panel_conteneur").remove();
				$("#js_assoc_panel").remove();
				$("body").append(x);
				class_assoc.relink();
				// dans certians cas on modifie la position 
				// du conteneur de panneau
				a = assoc_object.classe;
				if(a  == "mag2008" || a == "video_home" || a == "actuphonore_home" ){
					a = $("#chaude").height() + $("#mag").height() - 200;
					if (a <400) a=400;
					$("#assoc_panel_conteneur").css("top",a+"px");
				}
				if(a  == "home2008"  ){
					a = $("#chaude").height() +  - 200;
					if (a <400) a=400;
					$("#assoc_panel_conteneur").css("top",a+"px");
				}
				
			}
			if (class_assoc.req.method == "find"){
				$("#assoc_panel_resultat").html(x);
			}
	  },
      error : function(){
   		alert("<h1>Impossible de se connecter, contactez l'administrateur</h1>");
      }
  	});
}


/*
 * Fonction pour l'admin charger de realiser les associations entre 
 * des contenus -article, rubrique , sons ....- a d'autre type de
 * contenu 
 */
class_assoc.admin_assoc_ajax = {};
class_assoc.admin_assoc_action = function(){
	

	$.ajax({
      type: "POST", 
	  data: class_assoc.admin_assoc_ajax, 
	  url: '/spip.php?page=assoc_ajax_admin',
      success: function(x){
	  	if (class_assoc.admin_assoc_ajax.choix=="creer"){
	  		$("#assoc-admin-bloc-lien").html(x);
	  	/*
			$("#provi-lien").append(x);
			$("#assoc-liste").append($("#provi-lien .titre-lien-assoc-admin"));
			$("#assoc-zone-modif").append($("#provi-lien .contour"));*/
		}
	  	
	  },
      error : function(){
   		alert("<h1>Impossible de se connecter, contactez l'administrateur</h1>");
      }
  	});
	
	
}



/*
 * Dans la partie des methodes (prototypes) on va lister l'ensemble des
 * des fonctions associer en fonction du panel actif
 */

class_assoc.prototype = {
	
	// Le parametre de test permet de savoir si on pagine(renvoi false)
	// ou si on fait une nouvelle recherche
	recherche_rubrique : function(test){
		if (test) class_assoc.pagination = 0;
		rub =$("#rubrique_enfant option:selected").val();
		secteur = $("#rubrique_parent option:selected").val();
		/* if (rub=="" && secteur==""){
			//alert("Vous n'avez pas choisi de rubrique");
			class_assoc.req = {classe : assoc_object.classe , method : "find" , rubrique : rub , secteur : secteur 	, debut : debut , fin : fin , page : class_assoc.pagination};
			class_assoc.ajax();
			return;
		}
		*/
		// On va recuperer les dates de debut et de fin
		debut = $("#date_debut_assoc").val();
		fin = $("#date_fin_assoc").val();
		if (debut =="") debut = class_assoc.debut;
		if (fin =="") fin = class_assoc.fin;
		if (rub==""){
			a = $("#rubrique_enfant").find("[parent="+secteur+"]").size();
			$("#rubrique_enfant").find("[parent="+secteur+"]").each(function(x){
				rub += $(this).val();
				if (a!=(x+1)) rub+="|";
			});
		}
		
		class_assoc.req = {classe : assoc_object.classe , method : "find" , rubrique : rub , secteur : secteur 	, debut : debut , fin : fin , page : class_assoc.pagination};
		class_assoc.ajax();
	},
	
	pagination : function(x){
		if (class_assoc.pagination == 0 && x < 0) return;
		class_assoc.pagination += x;
		if (class_assoc.pagination < 0 ) class_assoc.pagination = 0;
		assoc_object.recherche_rubrique(false);
	},
	
	preselect : function(cet,type){
		class_assoc.type = type;
		
		// on masque le mode auto pour mag2008 quand 
		// on preselectionne un theme ou sous theme
		if (type!="article" && this.classe=="mag2008") $("#active_datepicker").css("display","none");
		
		
		if (type=="article"){
			a = $(cet).parent();
			titre = $(a).find(".titre").text();
			class_assoc.preselect_id = $(a).find(".titre").attr("value");
			$("#preselect_element").text(titre);
			
			// pour la partie magazine on rajoute la 
			// possibilile de mettre une date de passage en mode automatique
			if (this.classe=="mag2008")	$("#active_datepicker").css("display","block");
	
			
		}
		if (type=="rubrique"){
			ariane ="";
			provi ="";
	 		ariane =$("#rubrique_parent").find("option:selected").text();
			provi = $("#rubrique_enfant").find("option:selected").text();
			sous_theme = $("#rubrique_enfant").find("option:selected").val();
			if (sous_theme != "") ariane += ">" + provi;
			$("#preselect_element").text(ariane);
		}
	},


	associer : function(){
		// On verifie qu'un element est bien selectionnne
		if ($("#preselect_element").text()==""){
			alert("Vous n'avez s\351lectionn\351 aucun article ou \351l\351ment");
			return;
		}
		
		
		/* association pour les blocs home 2008*/
		if (class_assoc.type == "article" && assoc_object.classe == "home2008") {
			bloc.add("article");
			return;
		}
		if (class_assoc.type == "article" && assoc_object.classe == "mag2008") {
			titre = $("#preselect_element").text();
			date = $("#preselect_date").val();
			theme=$("input[name='automatique']:checked").val();
			taille =  parseInt( $("#actu_mag li").size() + 1);
			mag.req = {type : "new", choix : "article" ,id  : class_assoc.preselect_id, titre : titre , position : taille , date : date , theme : theme};
			mag.ajax();
			return;
		}
		
		
		
		
		
		
		ariane ="";
		provi ="";
		theme = $("#rubrique_parent").find("option:selected").val();
		sous_theme = $("#rubrique_enfant").find("option:selected").val();
		ariane = $("#preselect_element").text();
		
		if (class_assoc.type == "rubrique" && assoc_object.classe=="home2008") {
			taille = $("#actu_chaude .une_actu").size()+1;
			bloc.req = {type : "new", choix : "dynamic" , texte : ariane , theme  : theme , sous_theme : sous_theme ,  position : taille};
			bloc.ajax();
			return;
		}
		if (class_assoc.type == "rubrique" && assoc_object.classe=="mag2008") {
			taille = $("#actu_mag .une_actu").size()+1;
			mag.req = {type : "new", choix : "dynamic" , texte : ariane , theme  : theme , sous_theme : sous_theme ,  position : taille};
			mag.ajax();
			return;
		}
		
	},
	

	
	
}


