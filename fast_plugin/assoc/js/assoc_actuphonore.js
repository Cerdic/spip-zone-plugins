

class_assoc.prototype.omm_date = function(test){
		if(test) class_assoc.pagination = 0;
		debut = $("#date_debut_assoc").val();
		fin = $("#date_fin_assoc").val();
		if (debut =="") debut = class_assoc.debut;
		if (fin =="") fin = class_assoc.fin;
		class_assoc.req = {classe : assoc_object.classe , method : "find" , type :'date' ,debut : debut , fin : fin , page : class_assoc.pagination};
		class_assoc.ajax();
}

class_assoc.prototype.pagination_video = function(x){
		if (class_assoc.pagination == 0 && x < 0) return;
		class_assoc.pagination += x;
		if (class_assoc.pagination < 0 ) class_assoc.pagination = 0;
		assoc_object.omm_date(false);
}



class_assoc.prototype.preselect_titre_desc = function(id){
	a = $("#assoc_panel_resultat").find("#tr"+id);
	titre = $(a).find(".titre").text();
	$("#preselect_assoc_titre").val(titre);
	desc = $(a).find(".desc").text();
	$("#preselect_assoc_titre").val(titre);
	$("#preselect_assoc_desc").val(desc);
	class_assoc.preselect_id = $(a).find(".titre").attr("value");
}


class_assoc.prototype.efface_titre_desc = function(){
	$("#preselect_assoc_titre").val("");
	$("#preselect_assoc_desc").val("");
}



class_assoc.prototype.defaut_titre_desc = function(){
	assoc_object.preselect_titre_desc(class_assoc.preselect_id);
}


class_assoc.prototype.association = function(){
	a = class_assoc.preselect_id+"";
	if (a!="undefined") {
		id = class_assoc.id;
		id_lien = class_assoc.preselect_id;
		type_id = class_assoc.type_id;
		type_lien = assoc_object.classe;
		titre = $("#preselect_assoc_titre").val();
		texte = $("#preselect_assoc_desc").val();
		type = assoc_object.classe;
		class_assoc.admin_assoc_ajax = {choix : "creer", id : id ,id_lien : id_lien , type_id : type_id, type_lien : type_lien , titre : titre, texte : texte , type : type};
		class_assoc.admin_assoc_action();
	}
}

