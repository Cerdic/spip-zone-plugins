$(function(){
	actualiser_calculs();
	$("form").change(function(){
		actualiser_calculs();
	});
});

function actualiser_calculs() {
	$("form .saisie_calcul input[data-calcul]").each(function(){
		var expr = $(this).attr("data-calcul");
		avant = $(this).val();
		var resultat = eval(expr);
		if (avant != resultat) {
			$(this).val(resultat);
			$(this).trigger('change');
		}
	})
}
