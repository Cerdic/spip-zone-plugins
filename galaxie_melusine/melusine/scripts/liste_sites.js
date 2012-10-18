$(function() {
	$("span.date").each(
		function(){
			text=$(this).html();
			annee=text.substr(0,4);
			mois=text.substr(4,2);
			jour=text.substr(6,2);
			text=jour+"-"+mois+"-"+annee;
			$(this).html(text);
			return true;
		}
	)
}
)

 
$(function(){

text=$("span.tit_cat").html();
if (text=="accompagnement \xE9ducatif"||text=="tice"||text=="socle commun"||text=="orientation"||text=="\xE9ducation prioritaire"||text=="citoyennet\xE9"||text=="partenariats"||text=="participation"){
$('#acad').fadeIn("slow");


}



});

$(function(){ 
	$("a.acad").click(
		function(){
			$('#acad').fadeIn("slow");
			return false;
						
			}
			);
	}); 





$(function(){ 
	$("a.choix1").click(
		function(){
			$('#choix1').fadeIn("slow");
			
			$('#choix2').fadeOut("fast");
			
			$('#choix3').fadeOut("fast");
			
			$('#choix4').fadeOut("fast");
			
			$('#choix5').fadeOut("fast");
			return false;
			}
			);
	}); 

$(function(){ 
	$("a.choix2").click(
		function(){
			$('#choix2').fadeIn("slow");
			
			$('#choix1').fadeOut("fast");
			
			$('#choix3').fadeOut("fast");
			$('#choix4').fadeOut("fast");
			$('#choix5').fadeOut("fast");
			return false;
			}
			);
	}); 


$(function(){ 
	$("a.choix3").click(
		function(){
			$('#choix3').fadeIn("slow");
			
			$('#choix1').fadeOut("fast");
			
			$('#choix2').fadeOut("fast");
			
			$('#choix5').fadeOut("fast");
			$('#choix4').fadeOut("fast");
			return false;
			}
			);
	}); 

$(function(){ 
	$("a.choix4").click(
		function(){
			$('#choix4').fadeIn("slow");
			
			$('#choix1').fadeOut("fast");
			
			$('#choix2').fadeOut("fast");
			
			$('#choix3').fadeOut("fast");
			$('#choix5').fadeOut("fast");
			return false;
			
			}
			);
	}); 

$(function(){ 
	$("a.choix5").click(
		function(){
			$('#choix5').fadeIn("slow");
			
			$('#choix1').fadeOut("fast");
			
			$('#choix2').fadeOut("fast");
			
			$('#choix3').fadeOut("fast");
			$('#choix4').fadeOut("fast");
			return false;
			
			}
			);
	}); 






