<?php
function formulaires_choisir_theme_annonce_charger_dist($id_annonce='new',$tab_idth,$retour) {
	$valeurs = array();
	$id_themes = array();
	$req = sql_select('*','spip_themes_annonces','id_annonce='.sql_quote($id_annonce));
	while($r = sql_fetch($req)){
		$idth = $r['id_theme'];
		$id_themes[$idth] = 1;
	}
	
	$valeurs['redirect']=$retour;
	$valeurs['id_themes']=$id_themes;
	$valeurs['tab_idth']=$tab_idth;
	return $valeurs;
}

function formulaires_choisir_theme_annonce_verifier_dist($id_annonce='new',$retour) {
	return array();
}

function formulaires_choisir_theme_annonce_traiter_dist($id_annonce='new',$retour) {
	foreach (_request(id_themes) as $id_theme => $ok) {
		$thm_ann = sql_countsel('spip_themes_annonces','id_theme='.sql_quote($id_theme).' and id_annonce = '.sql_quote($id_annonce));
		if ($thm_ann == 0) sql_insertq('spip_themes_annonces',array('id_theme'=>$id_theme,'id_annonce'=>$id_annonce));
	}
	// gen modif, on fait des insertq mais aussi des delete : il faut donc gérer le cas inverse, et supprimer dans la base
	// des id_themes qui ont été décochées.
	return array('message_ok'=>'','redirect'=>$retour);
}

?>