function coordenadas (articulo){
	$.ajax({
		type: "POST",
		url: "'.generer_url_public('cambiar_coordenadas').'",
		data: "id_article="+articulo+"&lat="+document.forms.formulaire_coordenadas.lat.value+"&lonx="+document.forms.formulaire_coordenadas.lonx.value,
		success: function() {
		}
	});
}