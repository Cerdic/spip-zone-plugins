
function qcm_affichage_une_par_une(){

	var qcm_elements = $('div.qcm_element');
	
	// on cache tout
	qcm_elements.hide();
	
	// on crée les boutons
	var qcm_bouton_prec = $('<input type="button" value="&lt;&lt;&lt;" />').click(function(){
		$(this).parent().hide().prevAll('.qcm_element').eq(0).show();
	});
	var qcm_bouton_suiv = $('<input type="button" value="&gt;&gt;&gt;" />').click(function(){
		$(this).parent().hide().nextAll('.qcm_element').eq(0).show();
	});
	
	// on ajoute les boutons à chaque élément
	qcm_elements
		.filter(':first')
			.append(qcm_bouton_suiv)
			.end()
		.filter(':last')
			.append(qcm_bouton_prec)
			.end()
		.not(':first, :last')
			.append(qcm_bouton_prec)
			.append(qcm_bouton_suiv);
	
	// on affiche le premier élément
	qcm_elements.eq(0).show();
	
}
