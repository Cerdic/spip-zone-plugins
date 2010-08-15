<?php
/*
| 3/11/07 -
| affiche_milieu => exec=auteur_infos
*/

spipbb_log("included",3,__FILE__);

include_spip('inc/presentation');
include_spip('inc/minipres');
include_spip('inc/texte');
include_spip('inc/layer');

function spipbb_auteur_infos($id_auteur=0) {
	if (empty($id_auteur)) return;
	# spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee,
			$spip_lang_right,$spip_lang_left;

	$aff="";
	$aff.= "<div id='spipbb_editer_infos-$id_auteur'>";

	$visible = ($id_auteur==$connect_id_auteur) ;
	
	$bouton = bouton_block_depliable(_T('spipbb:config_champs_auteur'),$visible,"spipbb_$id_auteur");

	#$aff.= debut_cadre_relief(_DIR_PLUGIN_GAF."img_pack/gaf_ico-24.gif",true);
	$aff.=debut_cadre_enfonce(_DIR_PLUGIN_SPIPBB."img_pack/gaf_ico-24.gif", true, "", $bouton);

	$aff.= debut_block_depliable($visible,"spipbb_$id_auteur");


	$aff.= fin_block(true);
	$aff.= fin_cadre_enfonce(true);
	#$aff.= fin_cadre_relief(true);
	$aff.= "</div>";

	return $aff;

}

?>