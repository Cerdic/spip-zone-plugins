

sa = {};
sa.liste = new Array();
sa.liste["fonction"]= { sa : "php,inc,fct" , aa : "php,inc,fct,args_fct" };
sa.liste["objet"]= { sa_sa : "php,inc,class,method" , sa_aa : "php,inc,class,method,args_method" ,
	aa_sa : "php,inc,class,args_class,method" , aa_aa : "php,inc,class,args_class,method,args_method" };
sa.liste["fond"]= { sa : "fond,recup_fond" , aa : "fond,recup_fond,args_fond"};
sa.liste["datatype"]= { datatype : "datatype,dataType" };
sa.liste["final"]= {append : "final,append" , refresh : "final,refresh" , callback : "final,callback"};


sa.l_args = new Array();

/* Recuperation des champs en fonction de ce qui est defini dans sa.liste */
sa.value = function (obj,type){
	if(sa.liste[obj]){
		a = sa.liste[obj][type];
		a = a.split(",");
		cont = "#" + a[0];
		
		
		/* si ce n'est pas pour l'action final on propose un bouton pour vider */
		if(cont != "#final"){
			$(cont).html("");
			$(cont).append("<p class='red' onclick='sa.sup(this)'>X</p>")
		}else{
			if ($(cont).find("input[name='" + type + "']").size()==1) return;
		}
		for ( var i = 1; i < a.length; i++) {
			val = a[i];
			if(cont != "#final"){
				$(cont).append(val + ": <input type='text' name='"+val+"' ><br>")
			}else{
				$(cont).append("<p>" + val + ": <input type='text' name='"+val+"' ><span class='red' onclick='sa.sup(this)'>X</span></p>")
			}
		}
	}
}

sa.sup = function(cet){
	$(cet).parent().html("")
}

/* generation de la requete spip_ajax */
sa.generer = function (){
	/* on test qu'on genere quelque chose */
	taille = $("#champ input").size();
	if (taille==1)return;
	
	/* On remet les comptes a 0 */
	$("#recup_sa").html("");
	sa.l_args = new Array();
	test = true;
	
	chaine = "spip_ajax.req = ";
	provi ="{ ";
	
	/* Construction de la requete */
	$("#champ input").each(function(x){
		sep = ",";
		name = $(this).attr("name");
		val = $(this).val();
		

		if (x==taille-2) sep ="";
		
		/* recuperation des args si necessaire */
		if (name.substr(0,4)=="args"){
			a = val.split(",");
			for (i=0; i<a.length ; i++){
				if(!spip_ajax.in_array(sa.l_args,a[i])) sa.l_args.push(a[i]);
				if ($.trim(a[i])==""){
					alert("Vous avez un argument vide sur " + name);
					test = false;
				}
			}
		}
		

		
		/* construction de la requete si ce n'est ni une methode nio une class */
		if (x!=taille-1 && $.trim(val)!="" && name != 'method' && name != 'class') provi += " " + name + " : '" + val + "' " + sep;
		
		/* recuperation de la methode d'une classe */ 
		if (x!=taille-1 && $.trim(val)!="" && name == 'class') {
			method = $("#champ input[name='method']").val();
			val += ':'+ method;
			console.log(method + " test");
			provi += " " + name + " : '" + val + "' " + sep;
		}
			
	})
	/* on test qu'il n'y a pas d'erreur */
	if(!test)return;
	
	/* recuperation des args si necessaire */
	len = sa.l_args.length;
	args = "";
	if(len>0){
		for (i=0; i< len ; i++) {
			sep = ",";
			if (len==i-1) sep ="";
			a = sa.l_args[i];
			args += a + " : " + a + sep;
		}
	}
	
	provi +=' }';
	
	/* renvoie de la requete spip ajax*/
	chaine += args + provi + "; \nspip_ajax.ajax();\n\n";
	chaine += 'Ou en version abrégée \n\n$sa.spip_ajax(' + provi +  ')';
	$("#recup_sa").html(chaine);
}


