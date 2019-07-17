$(function(){
	actualiser_calculs();
	$("form").change(function(){
		actualiser_calculs();
	});
});

function actualiser_calculs() {
	$("form .saisie_calcul input[data-calcul]").each(function(){
		var expr = $(this).attr("data-calcul");
		var resultat = eval(expr);
		$(this).val(resultat);
	})
}
