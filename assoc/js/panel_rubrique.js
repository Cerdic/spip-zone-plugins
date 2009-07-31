


/* Association entre les 2 elements */
class_assoc.associer = function(){
	val = $("#les_rubriques").find("option:selected").val();
	if (val=="")return;
	
	spip_ajax.req = {inc : class_assoc.inc ,  
					class  : class_assoc.panel+":add" ,  
			 		refresh : '#liste_assoc_rubrique', 
			 		recup_fond : class_assoc.fond,
			 		args_fond : "id,type",
			 		type : class_assoc.type_id,
			 		id : class_assoc.id ,
			 		id_lien : val ,
			 		type_id : class_assoc.type_id , 
			 		type_lien : class_assoc.type_lien , 
			 		callback : 'class_assoc.menu_effect()'
		};
	spip_ajax.ajax();
}




