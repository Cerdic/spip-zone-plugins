<?php

function spipicious_insert_head($flux){
	global $visiteur_session;
	
	if($visiteur_session['id_auteur']){

	include_spip('selecteurgenerique_fonctions');
	$contenu = selecteurgenerique_verifier_js($flux);
	
	$selecteur = generer_url_public('selecteurs_tags');
    $tags_link = generer_url_public('inc-tags');
	
	$contenu .= <<<EOS
		<script type="text/javascript"><!--
			function deletetag(tag){
				var tag = tag;
				jQuery('input#remove_tag').val(tag).parents('form').submit().end();
				return false;
			}
	(function($) {
	var appliquer_selecteur_spipicious = function() {
		
		var spipicious = $('input[@name=spipicious_tags][autocomplete!=off]');
		// chercher l'input de saisie
		var id_objet = $("input#spipicious_id").val();
		var type = $("input#spipicious_type").val();
		if(spipicious.size()>0){
			$.ajax({
				type: "GET",
				url:'$tags_link&id_'+type+'='+id_objet,
				success:function(data,status){
					var newdata = jQuery(data+' #tags').html();
					jQuery('#tags').addClass('loading').html(newdata).removeClass('loading');
				}
			});
		}
		
		//.addClass('loading').load('$tags_link&id_'+type+'='+id_objet).removeClass('loading');
		spipicious.autocomplete('$selecteur',
			{
				extraParams:{
					id_objet:id_objet,
					type:type
				},
				delay: 200,
				autofill: false,
				minChars: 1,
				multiple:true,
				multipleSeparator:";",
				formatItem: function(data, i, n, value) {
					return data[0];
				},
				formatResult: function(data, i, n, value) {
					return data[1];
				},
			}
		);
		spipicious.result(function(event, data, formatted) {
			if (data[2] > 0) {
				jQuery(spipicious)
				.end();
			}
			else{
				return data[1];
			}
		});
		// Hack pour le focus obligatoire de positionner
		// Le selecteur generique ne se rechargeait pas
		spipicious.blur().focus();
	};
	
		$(function(){
			appliquer_selecteur_spipicious();
			onAjaxLoad(appliquer_selecteur_spipicious);
		});
	})(jQuery);
// --></script>
EOS;
	return $contenu;
	}
	else{return $flux;}
}

?>