<?php

include_spip('inc/autoriser');
include_spip('inc/utils');

function cog_boitier_cog($id,$table) {
	include_spip('inc/layer');
	include_spip('inc/presentation');

	$s="";
	$autoriser = autoriser("modifier",$table,$id);
	if($autoriser){
		$nb=sql_countsel("spip_cog_communes_liens", "objet=".sql_quote($table)." and id_objet=".$id);
		$deplier = ($nb > 0);
		$s .= debut_cadre_enfonce( "cog-24.png",true,'',bouton_block_depliable('<span style="text-transform: uppercase;">'._T('cog:communes').'</span>', $deplier, "cadre_communes"));
			$s .= debut_block_depliable($deplier,"cadre_communes");
		$s .= '<div id="cadre_communes" class="formulaire_spip formulaire_editer formulaire_communes">';
		$s .=  recuperer_fond("prive/editer/selecteur_commune", array('type'=>$table,'id'=>$id));
		$s .= '</div>';
		$s .= fin_block();
		$s .= fin_cadre(true);
	}
	return $s;
}

