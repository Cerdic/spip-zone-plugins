$(function(){
	build_donne();
})

function build_donne(){
	spip_ajax.req = {inc : "assoc_admin" , fct  : "get_liste_type_association" , 	
			 callback : "end_build_donne()"};
	spip_ajax.ajax();
}




function end_build_donne(){
	a = spip_ajax.retour.split("@");
	option = a[0];
	liste =  a[1];
	
	$("#pour_type").html(option);
	$("#avec_type").html(option);
	$("#liste_type_lien").html(liste);
	
	// on rajoute les types en vue d'une eventuelle suppression
	sup="";
	$("#pour_type option").each(function(){
		val = $(this).val();
		if(val!=""){
			sup +="<li>"+val+"&nbsp;&nbsp;<span class='delete_relation' onclick='delete_type(\""+val+"\")'>X</span></li>";
		}
	})
	$("#type_existant").html(sup);
	
	$("#ajout_type").val("");
	
	
}

function set_relation(){
	pour = $("#pour_type option:selected").val();
	avec = $("#avec_type option:selected").val();
	
	// on test que l'on a bien 2 choix
	if ( pour=="" || avec =="") return;
	
	// on test que la relation n'est pas existante
	if ( $("#liste_type_lien #"+pour+avec).size() ==1){
		alert("cette relation existe d\351ja");
		return;
	}
	
	spip_ajax.req = {inc : "assoc_admin" , fct  : "creer_relation" ,  args_fct : "pour,avec",	
					pour : pour , avec : avec , refresh : "#liste_type_lien"};
	spip_ajax.ajax();
}

function delete_relation(pour,avec){
	if(confirm ("Etes vous sur de vouloir supprimer ce lien ?")){
		spip_ajax.req = {inc : "assoc_admin" , fct  : "supprimer_relation" ,  args_fct : "pour,avec",	
				pour : pour , avec : avec , refresh : "#liste_type_lien"};
		spip_ajax.ajax();
	}
}

function ajout_type(){
	type = $("#ajout_type").val();
	
	// on test qu'une valeur a ete saisie
	if ( type=="") return;
	
	
	// on doit testter que ce type n'existe pas deja
	$("#pour_type option").each(function(){
			if (type==$(this).val()){
				alert("Ce type est d\351ja déclar\351 ")
				return;
	}});
	
	spip_ajax.req = {inc : "assoc_admin" , fct  : "creer_type" ,  args_fct : "type",	
			type : type , callback : "build_donne()" };
	spip_ajax.ajax();


}


function delete_type(type){
	// on verifie que le type n'est pas dans une relation existante
	test = true;
	
	$("#liste_type_lien li").each(function(){
		val = $(this).attr("id");
		if (val.indexOf(type)!=-1){
			alert("Vous ne pouvez supprimer ce type car il est en relation avec un autre \n supprimer cette relation avant ")
			test = false;
		}
	})
	if (!test)return;

	spip_ajax.req = {inc : "assoc_admin" , fct  : "supprimer_type" ,  args_fct : "type",	
			type : type , callback : "build_donne()" };
	spip_ajax.ajax();
}


