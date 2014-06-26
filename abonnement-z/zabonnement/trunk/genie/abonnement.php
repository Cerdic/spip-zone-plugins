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
	$where[] = "abos.ids_zone!=''";

	//on selectionne les abonnements en cours liés aux zones entrées dans leurs offres 
	$echeances = sql_allfetsel("contabo.id_contacts_abonnement,contabo.id_auteur,contabo.validite,abos.id_abonnement,abos.ids_zone",
		"spip_contacts_abonnements AS contabo LEFT JOIN spip_abonnements AS abos
		ON contabo.id_objet=abos.id_abonnement
		",$where);
	
	foreach($echeances as $echu){
		$id_contabo=$echu['id_contacts_abonnement'];
		$id_auteur=$echu['id_auteur'];
		$id_abonnement=$echu['id_abonnement'];
		$ids_zone=$echu['ids_zone'];
		$validite=$echu['validite'];
		
		//si un même auteur a plusieurs fois la même offre, on doit traiter la plus récente uniquement						
		$dernier_id_contacts_abonnement = sql_getfetsel(
			'id_contacts_abonnement','spip_contacts_abonnements',
			array(
				'id_auteur = '.sql_quote($id_auteur),
				'objet = "abonnement"',
				'id_objet = '.sql_quote($id_abonnement)
			),'',array('validite'." DESC "),"0,1"
		);
										
		//ne traiter que le dernier abonnement (par exemple si il a déjà été repris)
		if ($id_contabo==$dernier_id_contacts_abonnement){
					
			//on ferme les zones si la date de validite est passée
			if($validite<$now){
				fermer_zone($id_auteur,$ids_zone);
				
				if (_DEBUG_ABONNEMENT) spip_log("Pour auteur $id_auteur fermer zones ($ids_zone) id_contacts_abonnement $id_contabo validite=$validite",'abonnement_fermer');
				
				//on note echu le statut abonnement	
				sql_updateq("spip_contacts_abonnements",array('statut_abonnement'=>'echu'),"id_contacts_abonnement='$id_contabo'");
						
			}
			
			//on ouvre les zones si la date validite est à venir
			if($validite>$now){
				ouvrir_zone($id_auteur,$ids_zone);
				
				if (_DEBUG_ABONNEMENT) spip_log("Pour auteur $id_auteur ouvrir zones ($ids_zone) id_contacts_abonnement $id_contabo validite=$validite",'abonnement_ouvrir');
				
			}
		}
		
	}
	
return 1;

}

?>