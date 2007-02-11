<?php
//
// action/editer_digg.php
//

include_spip("inc/presentation");

function action_editer_digg_dist(){	
	$insert = false;
	if (!$id_digg = intval($arg)) {
		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		if (lire_meta('spipdigg_type_moderation')  != 0)  { $date_validation = "0000-00-00 00:00:00"; }else{ $date_validation = "CURRENT_TIMESTAMP"; }
		$id_rubrique = _request('id_rubrique');
		$id_secteur = '';
		$sql_insert_digg = "INSERT INTO spip_diggs SET id_digg='', titre='".addslashes(_request('titre'))."', descriptif='".addslashes(_request('texte'))."', url_digg='".addslashes(_request('url_digg'))."', id_rubrique='".$id_rubrique."', id_secteur='".$id_secteur."', date=CURRENT_TIMESTAMP, date_modif=CURRENT_TIMESTAMP, hits='0', points='0', date_validation='".$date_validation."', statut='prepa';";
		$res_insert_digg = spip_query($sql_insert_digg);
		$id_digg = mysql_insert_id();
		$sql_lier_digg_auteur = "INSERT INTO spip_diggs_auteurs SET id_digg='".$id_digg."', id_auteur='".$id_auteur."';";
		$res_lier_digg_auteur = spip_query($sql_lier_digg_auteur);
		if ($res_insert_digg && $res_lier_digg_auteur) $insert = true;
		//echo $sql_insert_digg;
	}else{
		$id_digg = _request('id_digg');
	}
	
	$redirect = parametre_url(urldecode(_request('redirect')),'id_digg', $id_digg);
	redirige_par_entete($redirect);
}
?>
