
function masque_site(id){
	$("#"+id).hover(function(){
 		 $(this).css("display","block").css("visibility","visible");
	},function(){
  		$(this).css("display","none").css("visibility","hidden");
	});
}



function obj_assoc(obj){
	this.obj = obj;
	//console.debug(this);
}

obj_assoc.req = {};
obj_assoc.deplie = true;

obj_assoc.ajax = function (){
	$.ajax({
      type: "POST", 
	  data: obj_assoc.req , 
	  url: '/spip.php?page=assoc_ajax_admin',
      success: function(x){
	  	
			
	  },
      error : function(){
   		alert("<h1>Impossible de se connecter, contactez l'administrateur</h1>");
      }
  	});
}


obj_assoc.crayon_titre ="&nbsp;<img class='align-middle' src='../plugins/assoc/img/crayon.png'/><input type='text' class='invisible letitre' />";
obj_assoc.crayon_texte ="&nbsp;<img class='align-middle' src='../plugins/assoc/img/crayon.png'/><textarea rows='3' cols='15' class='invisible letexte' ></textarea>";

obj_assoc.prototype = {
	deplier : function(){
		if (!obj_assoc.deplie){
			obj_assoc.deplie = true;
			return;
		}
	
		if ($("#modif"+this.obj).css("display") == "none"){
			$(".display-none").css("display","none");
			$("#modif"+this.obj).css("display","block");
		}else{
			$(".display-none").css("display","none");
		}
	},
	
	titre : function(){
		zone = $("#modif"+this.obj).find(".letitre");
		if ($(zone).hasClass("visible")){
			// On modifie les differents titre
			// et on envoit une requete ajax
			titre = $(zone).val();
			obj_assoc.req = {choix : "titre" , id : this.obj , titre : titre };
			obj_assoc.ajax();
			$("#modif"+this.obj).find(".le_titre").html(titre+obj_assoc.crayon_titre);
			$("#lien"+this.obj).find(".titre_aff").text(titre);
			$(zone).addClass("invisible");
		}else{
			titre = $("#modif"+this.obj).find(".le_titre").text();
			titre = $.trim(titre);
			$(zone).addClass("visible").removeClass("invisible").val(titre);
		}
	},
	
	texte : function(){
		zone = $("#modif"+this.obj).find(".letexte");
		if ($(zone).hasClass("visible")){
			// On modifie les differents titre
			// et on envoit une requete ajax
			texte = $(zone).val();
			obj_assoc.req = {choix : "texte" , id : this.obj , texte : texte };
			obj_assoc.ajax();
			$("#modif"+this.obj).find(".le_texte").html(texte+obj_assoc.crayon_texte);
			$(zone).addClass("invisible");
		}else{
			texte = $("#modif"+this.obj).find(".le_texte").text();
			texte = $.trim(texte);
			$(zone).addClass("visible").val(texte);
			$(zone).addClass("visible").removeClass("invisible").val(texte);
		}
		
	},
	
	supprimer : function(){
		if (confirm("Etes vous sur de vouloir supprimer le lien ?")){
			obj_assoc.req = {choix : "supprimer" , id : this.obj };
			obj_assoc.ajax();
			$("#modif"+this.obj).remove();
			$("#lien"+this.obj).remove();
		}
		
	},
	
	
	inserer : function (id){
		a = "<omm"+  id + ">";
		texte = $('.cadre-formulaire').find('textarea:eq(2)').val() + a;
		$('.cadre-formulaire').find('textarea:eq(2)').val(texte);
		obj_assoc.deplie = false;
	}
	
}





