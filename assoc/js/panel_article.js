/* Appel pour la recherche*/
class_assoc.rechercher = function(init){
	rub = $("#les_rubriques").find("option:selected").val();
	if (!class_assoc.actif || rub == "" )return;
	if (init) class_assoc.page = 0;
	debut = $("#date_debut_assoc").val();
	if (debut=="")debut = class_assoc.debut ;
	fin = $("#date_fin_assoc").val();
	if (fin=="")fin = class_assoc.fin ;
	
	spip_ajax.req = {inc : "assoc_panel" ,  class  : class_assoc.panel+":find" ,  
				refresh : '#panel_resultat', rubrique : rub , page : class_assoc.page ,
				debut : debut , fin : fin};
	spip_ajax.ajax();
}



/* Association entre les 2 elements */
class_assoc.associer = function(val){
	spip_ajax.req = {inc : class_assoc.inc ,  
					 class  : class_assoc.panel+":add" ,  
					 		refresh : '#liste_assoc_article', 
					 		recup_fond : class_assoc.fond,
					 		args_fond : "id,type",
					 		id : class_assoc.id ,
					 		type : class_assoc.type_id,
					 		id_lien : val ,
					 		type_id : class_assoc.type_id , 
					 		type_lien : class_assoc.type_lien , 
					 		callback : 'class_assoc.menu_effect()'
					 };
	spip_ajax.ajax();
}




