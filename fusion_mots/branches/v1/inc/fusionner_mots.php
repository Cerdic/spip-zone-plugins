<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function fusionner_mots($source,$cible){
	// $source 	=> 	array des id_mot
	// $cible	=>	id_mot	

	$objets = sql_allfetsel('DISTINCT objet', 'spip_mots_liens');

	
	foreach($objets as $objet){
		$objet = sql_quote($objet['objet']);
		
	
		foreach ($source as $id_mot){
			if ($id_mot !=$cible){
				
				// pour éviter les entrées double, vérifier les liens déjà existant
				$liens_existants 	= sql_allfetsel('id_objet','spip_mots_liens','id_mot='.intval($cible).' and objet='.$objet);

		
				$liens_existants_formates = array();
				foreach ($liens_existants as $lien){
					$liens_existants_formates[] = $lien['id_objet'];
				}
				$liens_existants_formates = implode($liens_existants_formates,',');
				
				// On met à jour, sauf quand le lien est déjà existant
				$where = 'id_mot='.intval($id_mot).' and objet='.$objet;
				if ($liens_existants_formates != ''){
					$where.=  ' and '. sql_in('id_objet',$liens_existants_formates,'NOT')	;
				}	
				sql_update('spip_mots_liens', array("id_mot"=>sql_quote($cible)),$where);	
				// on supprime les anciens liens qui existent encore, ceux qu'on n'a pas modifié pour cause de duplicata
				sql_delete('spip_mots_liens','id_mot='.$id_mot.' and objet='.$objet);
			
			}
		}
	}
	// suppression des mots
	sql_delete('spip_mots','id_mot!='.$cible.' and '.sql_in('id_mot',$source)); 
}
?>