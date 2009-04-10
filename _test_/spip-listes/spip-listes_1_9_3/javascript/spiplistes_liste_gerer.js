// spiplistes_liste_gerer.js
// utilise' par _SPIPLISTES_EXEC_LISTE_GERER

// $LastChangedRevision: 15830 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2007-10-07 05:41:13 +0200 (dim., 07 oct. 2007) $

jQuery(document).ready(function() {
	var alerter_modif_statut = false;
	// [@attr] style selectors removed in jQuery 1.3 (voir http://docs.jquery.com/Selectors)
	// rendre compatible avec anciennes versions de jQuery
	var jqss = (jQuery.fn.jquery < '1.3') ? '@' : '';
	$('#change_statut').change(function() {
		switch($(this).val()) {
			case '"._SPIPLISTES_PRIVATE_LIST."':
				if(!alerter_modif_statut) { 
					alert('".spiplistes_texte_html_2_iso(_T('spiplistes:Attention_action_retire_invites'), $GLOBALS['meta']['charset'], true)."'); 
					alerter_modif_statut=true; 
				}
				$('#img_statut').attr('src','".spiplistes_items_get_item("puce", _SPIPLISTES_PRIVATE_LIST)."');
				break;
			case '"._SPIPLISTES_PUBLIC_LIST."':
				$('#img_statut').attr('src','".spiplistes_items_get_item("puce", _SPIPLISTES_PUBLIC_LIST)."');
				break;
			case '"._SPIPLISTES_TRASH_LIST."':
				$('#img_statut').attr('src','".spiplistes_items_get_item("puce", _SPIPLISTES_TRASH_LIST)."');
				break;
		}
	});
	// interactivite bloc planification
	$('#auto_oui').change(function(){
		$('#auto_oui_detail').toggle();
	});
	$('#auto_non').change(function(){
		$('#auto_oui_detail').toggle();
	});
	$('input['+jqss+'name=auto_chrono]').change(function(){
		$('#auto_weekly').attr('checked',false);
		$('#auto_mois').attr('checked',false);
	});
	$('input['+jqss+'name=periode]').focus(function(){
		$('#auto_weekly').attr('checked',false);
		$('#auto_mois').attr('checked',false);
		$('input['+jqss+'name=auto_chrono]['+jqss+'value=auto_jour]').attr('checked','checked');
	});
	$('#auto_weekly').change(function(){
		$('input['+jqss+'name=periode]').val('0');
		$('#auto_mois').attr('checked',false);
		$('input['+jqss+'name=auto_chrono]['+jqss+'value=auto_hebdo]').attr('checked','checked');
	});
	$('#auto_mois').change(function(){
		$('input['+jqss+'name=periode]').val('0');
		$('#auto_weekly').attr('checked',false);
		$('input['+jqss+'name=auto_chrono]['+jqss+'value=auto_mensuel]').attr('checked','checked');
	});
	/*
	* forcer le format de reception ? 
	*/
	$('input['+jqss+'name=forcer_abo]').click( function() { 
		if($(this).val() == 'aucun') {
			$('#forcer_format').hide();
		}
		else {
			$('#forcer_format').show();
		}
	});
	$('input['+jqss+'name=forcer_format_reception]').click( function() { 
		$('#forcer_format_abo').attr('checked','checked');
	});
	// sparadra !
	// fait disparaitre la legende si deplie'
	var sparadra;
	$('#triangle1').click( function() { 
		if(!sparadra) {
			$('#legend-abos1').html('');
			sparadra = true;
		}
		else {
			$('#legend-abos1').html($('#legend-abos1-propre').html());
			sparadra = false;
		}
	});
});