<?php

/**
 * spip.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 * Erational
 *
 * © 2007-2011 - Distribue sous licence GNU/GPL
 * 
 * Fichiers des pipelines du plugin
 */

/**
 * Insertion du code javascript nécessaire dans le head
 *
 * @param string $flux
 * @return
 */
function spipicious_insert_head($flux){
	global $visiteur_session;
	include_spip('inc/autoriser');
	if(autoriser('tagger_spipicious','article',$id_objet,$visiteur_session,$opt)){

	include_spip('selecteurgenerique_fonctions');
	$flux .= selecteurgenerique_verifier_js($flux);

	$selecteur = generer_url_public('selecteurs_tags');
    $tags_link = generer_url_public('inc-tags');

	$flux .= 
<<<EOS
<script type="text/javascript"><!--
	(function($) {
		var spipicious_call = 0;
		var appliquer_selecteur_spipicious = function() {

			// chercher l'input de saisie
			var spipicious = $('input[name=spipicious_tags][autocomplete!=off]');

			var id_objet = $("input#spipicious_id").val();
			var type = $("input#spipicious_type").val();
			if((spipicious.size()>0) && ($('.tags_'+type+'_'+id_objet).size()>0) && (spipicious_call > 1)){
				$.ajax({
					type: "GET",
					url:'$tags_link',
					data: {
						id_objet : id_objet,
						objet : type
					},
					success:function(data,status){
						var newdata = jQuery(data+' #tags').html();
						$('.tags_'+type+'_'+id_objet).addClass('loading').html(newdata).removeClass('loading');
						$('body').trigger('spipicious_change');
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
					}
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
			spipicious_call++;
			// Hack pour le focus obligatoire de positionner
			// Le selecteur generique ne se rechargeait pas
			spipicious.blur();
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

/**
 * Insertion dans le pipeline optimiser_base_disparus (SPIP)
 * 
 * Supprimer les liens spipicious/objet sur les éléments disparus
 * @param array $flux le contexte du pipeline
 */
function spipicious_optimiser_base_disparus($flux){
	/**
	 * On fonctionne comme les documents dans genie/optimiser
	 */
	$r = sql_select("DISTINCT objet","spip_spipicious");
	while ($t = sql_fetch($r)){
		$type = $t['objet'];
		$spip_table_objet = table_objet_sql($type);
		spip_log($spip_table_objet,'test');
		$id_table_objet = id_table_objet($type);
		$res = sql_select("L.id_mot AS id,L.id_objet AS id_objet",
			      "spip_spipicious AS L
			        LEFT JOIN $spip_table_objet AS O
			          ON O.$id_table_objet=L.id_objet AND L.objet=".sql_quote($type),
				"O.$id_table_objet IS NULL");
		while ($row = sql_fetch($res)){
			sql_delete("spip_spipicious", array("id_mot=".$row['id'],"id_objet=".$row['id_objet'],"objet=".sql_quote($type)));
			spip_log("Entree ".$row['id']."/".$row['id_objet']."/$type supprimee dans la table spip_spipicious",'test');
		}
	}
	return $flux;
}
?>