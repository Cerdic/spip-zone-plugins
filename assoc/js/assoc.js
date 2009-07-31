$(function(){
	class_assoc.menu_effect();
})


// Utilisation du datepicker : on peux proposer un ou deux datepicker
// en leur donnat les id  #date_debut_assoc et/ou date_fin_assoc

// inc 		 -> repertoire inc qui va etre inclus pour l'appel du panneau et d'autre traitement
// panel 	 -> nom de la classe qui etend assoc_panel dont les methode vont etre utiliser
// fond 	 -> fond a recuperer apres une action sur une association (ajout)
// id 		 ->  id de l'article ,par exemple , auquel on va associer quelque chose	
// type_id   -> type d'element auxquel on va associer quelque chose (par exemple article)
// type_lien -> type d'element qui est associe 
// dir  	 -> on souhaite utiliser des elements qui sont dans un autre repoire pour le js / css
// js 		 -> on souhaite utiliser un autre js que celui par defaut
// css		 -> on souhaite utiliser un autre css que celui par defaut
function class_assoc(inc, panel,fond,id,type_id,type_lien,dir,js,css ){
	class_assoc.inc=inc;
	class_assoc.panel=panel;
	
	/* parametre optionnel */
	fond = (fond) ? class_assoc.fond=fond : class_assoc.fond="";
	id = (id) ? class_assoc.id=id : class_assoc.id="";
	type_id = (type_id) ? class_assoc.type_id=type_id : class_assoc.type_id="";
	type_lien = (type_lien) ? class_assoc.type_lien=type_lien : class_assoc.type_lien="";

	/* parametre permettant d'appeler d'autres js , css ,dans un autre repertoire */
	/* ce sont egalement des parametres optionnels */
	args_class = "panel";
	if (dir)  args_class +=",dir";
	if (js)  args_class +=",js";
	if (css) args_class +=",css";

	
	class_assoc.actif = true;
	$("#panel_conteneur").remove();
	spip_ajax.req = {inc : class_assoc.inc ,  args_class : args_class , class  : panel+":get_panel" 
					, panel : panel , append : 'body' , callback :'class_assoc.relink()'};
	if (dir) spip_ajax.req.dir =dir;
	if (js) spip_ajax.req.js =js;
	if (css) spip_ajax.req.css =css;
	
	spip_ajax.ajax();
}

/* Variable de la classe class_assoc */ 
// verifie qu'un panneau est bien charge
class_assoc.actif = false;
// cursuer pour la pagination
class_assoc.page = 0;
// date par defaut
class_assoc.debut = "1990-01-01";
class_assoc.fin = "";
// element preselectionne
class_assoc.preselect = 0;

// les elements propres a l'association
class_assoc.panel="";
class_assoc.fond="";
class_assoc.id="";
class_assoc.id_lien="";
class_assoc.type_id="";
class_assoc.type_lien="";
class_assoc.inc = "";



/* On modifie la date de fin dynamiquement */
class_assoc.date_evaluate = function (){
	demain = new Date();
	demain.setTime(demain.getTime() + 24 * 3600 * 1000);
	class_assoc.fin = demain.getFullYear()+"-"+(demain.getMonth()+1)+"-"+demain.getDate();
}

class_assoc.date_evaluate();



//on masque le panneau d'assoication
class_assoc.close = function(){
	$("#panel_conteneur").remove();
	class_assoc.actif = false;
}


// fonction appeler lors du chargement de la page
class_assoc.relink = function(){
	
	// On supprime les actions existantes
	$("#panel_conteneur").unbind("draggable");
	$('#date_debut_assoc').datepicker("destroy");
	$('#date_fin_assoc').datepicker("destroy");
	
	//et on relance
	$('#date_debut_assoc').datepicker({  changeMonth: true, changeYear: true });
	$('#date_fin_assoc').datepicker({  changeMonth: true, changeYear: true });
	$("#ui-datepicker-div").css("z-index","500000");
	$("#panel_conteneur").draggable();
	
	// on met la pagination a 0
	class_assoc.page= 0;
}

/* fonction qui gere la pagination et qui appel la fonction de recherche  */
class_assoc.pagination = function(x){
	if (class_assoc.page == 0 && x < 0) return;
	class_assoc.page += x;
	if (class_assoc.page < 0 ) class_assoc.page = 0;
	class_assoc.rechercher(false);
}

/* fonction qui supprime un lien a partir du 'id' cle */ 
class_assoc.supprimer = function(cle,id,sup){
	if(confirm ("Etes vous sur de vouloir supprimer ce lien ?")){
		spip_ajax.req = {inc : "assoc_panel" ,  fct  : "delete_association" , cle : cle , id : id };
		$('#'+sup).remove();
		spip_ajax.ajax();
	}
}

class_assoc.menu_effect = function(){
	$("#type_association  li").hover( function () {$(this).addClass("hover") },
			function () {$(this).removeClass("hover") });
}




