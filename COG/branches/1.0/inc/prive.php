<?php


include_spip('inc/vieilles_defs');
include_spip('inc/autoriser');
include_spip('inc/utils');

function cog_boitier_cog($id,$table) {
	global $spip_lang_left, $spip_lang_right;
	// on recupere l'id de l'auteur en cours
	if ($GLOBALS["auteur_session"])
		$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
	// et on verifie qu'il est autorisé à modifier l'élément en cours
	$autoriser = autoriser("modifier",$table,$id);
	if($autoriser){
		$nb=sql_countsel("spip_cog_communes_liens", "objet=".sql_quote($table)." and id_objet=".$id);
		$deplier = ($nb > 0);
		$s .= debut_cadre_enfonce( _DIR_PLUGIN_COG."images/cog-24.png",true,'',bouton_block_depliable('<span style="text-transform: uppercase;">'._T('cog:communes').'</span>', $deplier, "cadre_communes"));
		$s .= debut_block_depliable($deplier,"cadre_communes");
		$s .= '<div id="cadre_communes" class="formulaire_spip formulaire_editer formulaire_communes" style="margin-bottom:5px;">';
		$s .=  recuperer_fond("prive/editer/selecteur_commune", array('type'=>$table,'id'=>$id));
		$s .= '</div>';
		$s .= fin_block();
		$s .= fin_cadre(true);
	}
	return $s;
}



?>
