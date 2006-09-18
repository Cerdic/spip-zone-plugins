$(document).ready(function(){
  //$('div.flash').remove();
  $('object').wrap('<div class="flashie"></div>');
  
	$('div.flashie').each(function(){
		var group=this;
		// recuperer les tags param car IE les perds lors de innerHTML si l'attribut data de la balise object est utilise
		var paramcode="";
		$('param',group).each(function(){
			paramcode = paramcode +'<param name="'+this.name+'" value="'+this.value+'" />';
		});
		var code = this.innerHTML;
		//this.innerHTML="";
		// ajouter les attributs juste avant la fermeture de la balise object
		var reg=new RegExp("(<\/object>)", "i");
		code = code.replace(reg,paramcode+"$1");
		this.innerHTML=code;
	});
  
});