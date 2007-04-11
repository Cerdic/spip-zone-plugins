<?php
//
// sauver_categorie.php
//
if (!defined("_ECRIRE_INC_VERSION")) return;


function action_sauver_categorie_dist(){
	
	$res_id_secteur_lang = mysql_fetch_array(spip_query("SELECT id_secteur, lang FROM spip_boutique_categories WHERE id_categorie = '"._request('id_parent')."';"));
	$id_secteur = $res_id_secteur_lang['id_secteur'];
	
	if (_request('id_categorie') == 'new'){
		$lang = $res_id_secteur_lang['lang'];
		$sql_insert_categorie = "INSERT INTO spip_boutique_categories ";
		$sql_insert_categorie .= "(id_categorie,titre,descriptif,texte,logo,lang,id_parent,id_secteur,date,date_modif) ";
		$sql_insert_categorie .= "VALUES ('','".addslashes(_request('titre'))."','".addslashes(_request('descriptif'))."','".addslashes(_request('texte'))."','','".$lang."','"._request('id_parent')."','".$id_secteur."',CURRENT_TIMESTAMP,'');";
		
		$res_insert_categorie = spip_query($sql_insert_categorie);
		$id_categorie = mysql_insert_id();
		
		if ($id_secteur == 0){ $res_fix_secteur = spip_query("UPDATE spip_boutique_categories SET id_secteur = '".$id_categorie."' WHERE id_categorie='".$id_categorie."';"); }
	}else{
		$sql_update_categorie = "UPDATE spip_boutique_categories ";
		$sql_update_categorie .= "SET titre='".addslashes(_request('titre'))."', descriptif='".addslashes(_request('descriptif'))."', texte='".addslashes(_request('texte'))."', lang='"._request('lang')."', id_parent='"._request('id_parent')."', id_secteur='".$id_secteur."' ";
		$sql_update_categorie .= "WHERE id_categorie = '"._request('id_categorie')."';";
		
		$res_update_categorie = spip_query($sql_update_categorie);
		$id_categorie = _request('id_categorie');
	}

	echo $sql_insert_categorie.'<br /><br /><br /><br />';
	echo $sql_update_categorie;
	$redirect = _request('redirect').'&id_categorie='.$id_categorie;
	redirige_par_entete($redirect);

}
?>
