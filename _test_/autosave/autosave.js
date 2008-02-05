$(document).ready(function(){

	$("#text_area").css("color","#FF0000").parent().append("<div style='float:right;margin-top:10px' id='div_result'></div><input id='autosave' type='submit' name='autosave' value='hop'>").keypress(function(){
	
	$("#autosave").removeAttr("disabled");
	
	});


	$('#autosave').click(saveauto);


});

saveauto = function(e){

e.preventDefault();

var text_a_sauver = $("#text_area").val() ;
var id_article = $("form[@name='formulaire'] input[@name='arg']").val();

url = './?exec=autosave' ;

$.ajax({
   type: "POST",
   url: url,
   data: "texte="+text_a_sauver+"&id_article="+id_article,
   success: function(msg){
     $("#div_result").html(msg);
     $("#autosave").attr("disabled","disabled");
   }
 });

}