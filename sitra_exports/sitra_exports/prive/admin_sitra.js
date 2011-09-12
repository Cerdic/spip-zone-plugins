$(document).ready(function() {
	/* les liens externes dans une nouvelle fenêtre */
	$('a.objet_sitra').attr('target','_blanck');
	/* replier les listes */
	$('#liste_objets > dd').hide();
	$('#liste_objets > dt strong').click(function(){
		var el_dd = $(this).attr('id');
		$('#liste_objets > dd.'+el_dd).toggle();
	})
	$('.afficher_masquer').click(function(){
		$('#liste_objets > dd').toggle();
	});
});