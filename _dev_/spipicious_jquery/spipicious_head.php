<?php

/*
 * spipicious
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * Quentin Drouet
 * Erational
 * 
 * 2007-2008 - Distribue sous licence GNU/GPL
 *
 */
 
function spipicious_insert_head($flux){
	global $visiteur_session;
	$contenu = " ";
	$autorise = lire_config('spipicious/people');
	
	if($visiteur_session['id_auteur'] && in_array($visiteur_session['statut'],$autorise)){

	include_spip('selecteurgenerique_fonctions');
	$flux .= selecteurgenerique_verifier_js($flux);
	
	$selecteur = generer_url_public('selecteurs_tags');
    $tags_link = generer_url_public('inc-tags');
	
	$flux .= <<<EOS
		<script type="text/javascript"><!--
		
		var deletetag = function(tag){
				jQuery('input#remove_tag').val(tag).parents('form').submit().end();
				return false;
		};
		
	(function($) {
		var appliquer_selecteur_spipicious = function() {
			
			// chercher l'input de saisie
			var spipicious = $('input[name=spipicious_tags][autocomplete!=off]');
			
			var id_objet = $("input#spipicious_id").val();
			var type = $("input#spipicious_type").val();
			if((spipicious.size()>0)&&(type!='')){
				$.ajax({
					type: "GET",
					url:'$tags_link',
					data: 'id_'+type+'='+id_objet,
					success:function(data,status){
						var newdata = jQuery(data+' #tags').html();
						$('.tags').addClass('loading').html(newdata).removeClass('loading');
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
					$(spipicious)
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
}
	return $flux;
}

?>