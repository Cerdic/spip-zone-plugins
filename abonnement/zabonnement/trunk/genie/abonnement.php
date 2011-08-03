<?php
/**
 * Plugin Abonnement pour Spip 2.0+
 * Licence GPL
 * 
 *
 */

function abonnement_dist($t){
	
	# les abonnements (avec zones) arrives a echeance
	$where = array();
	$where[] = "contabo.objet='abonnement'";
	$where[] = "contabo.validite<'".date('Y-m-d H:i:s')."'";
	$where[] = "contabo.statut_abonnement!='echu'";

	$echeances = sql_allfetsel("contabo.id_contacts_abonnement,contabo.id_auteur,contabo.validite,abos.ids_zone",
		"spip_contacts_abonnements AS contabo LEFT JOIN spip_abonnements AS abos
		ON contabo.id_objet=abos.id_abonnement
		",$where);
	foreach($echeances as $echu){
		$id_contabo=$echu['id_contacts_abonnement'];
		$id_auteur=$echu['id_auteur'];
		$ids_zone=$echu['ids_zone'];
		$validite=$echu['validite'];
		
		//on ferme les zones
		fermer_zone($id_auteur,$ids_zone);
		
		//on note echu le statut abonnement	
		sql_updateq("spip_contacts_abonnements",array('statut_abonnement'=>'echu'),"id_contacts_abonnement='$id_contabo'");
		
		spip_log("Pour auteur $id_auteur fermer zones ($ids_zone) abonnement $id_contabo validite=$validite",'abonnement');
	}
	
return 1;

}

?>