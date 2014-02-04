<?php
/**
 * Plugin Abonnement pour Spip 2.0+
 * Licence GPL
 * 
 *
 */

function genie_abonnement_dist($t){
	
	include_spip('action/editer_contacts_abonnement');
	
	// vérifier les zones pour spip_contacts_abonnements
	$now = date('Y-m-d H:i:s');
	$where = array();
	$where[] = "contabo.objet='abonnement'";
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
		
		//on ferme les zones si la date de validite est passée
		if($validite<$now){
		fermer_zone($id_auteur,$ids_zone);
		
		//on note echu le statut abonnement	
		sql_updateq("spip_contacts_abonnements",array('statut_abonnement'=>'echu'),"id_contacts_abonnement='$id_contabo'");
		
		}
		
		//on ouvre les zones si la date validite est à venir
		if($validite>$now){
		ouvrir_zone($id_auteur,$ids_zone);
		}
		
		if (_DEBUG_ABONNEMENT) spip_log("Pour auteur $id_auteur fermer zones ($ids_zone) abonnement $id_contabo validite=$validite",'abonnement');
	}
	
return 1;

}

?>