$(function(){
	actualiser_calculs();
	$("form").change(function(){
		actualiser_calculs();
	});
});
function calcul_arrondi(nb, precision = 0) {
	nb = nb * Math.pow(10, precision);
	nb = Math.round(nb);
	nb = nb / Math.pow(10, precision);
	return nb;
}
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
