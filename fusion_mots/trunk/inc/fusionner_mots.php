<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function fusionner_mots($source,$cible){
	// $source 	=> 	array des id_mot
	// $cible	=>	id_mot	
	
	$objets = array('article','breve','document','forum','rubrique','syndic');
	
	foreach ($objets as $objet){
		$table = 'spip_mots_'.table_objet($objet);
		if ($objet == 'forum') {// une petite exception
			$table = 'spip_mots_forum';
		}	
		// pour éviter les entrées double, vérifier les liens déjà existant
		$liens_existants 	= sql_allfetsel('id_'.$objet,$table,'id_mot='.intval($cible));
		
		$liens_existants_formates = array();
		foreach ($liens_existants as $lien){
			$liens_existants_formates[] = $lien['id_'.$objet];
		}
		$liens_existants_formates = implode($liens_existants_formates,',');
		
		
		foreach ($source as $id_mot){
			if ($id_mot !=$cible){
				// On met à jour, sauf quand le liens est déjà existant
				$where = 'id_mot='.intval($id_mot);
				if ($liens_existants_formates != ''){
					$where.=  ' and '. sql_in('id_'.$objet,$liens_existants_formates,'NOT')	;	
					
				}
				sql_update($table, array("id_mot"=>sql_quote($cible)),$where);	
				// on supprime les anciens liens qui existent encore, ceux qu'on n'a pas modifié pour cause de duplicata
				sql_delete($table,'id_mot='.$id_mot);
				
				// On supprime le mot
				sql_delete ('spip_mots','id_mot='.$id_mot);
			}
		}
	}
	

	
}
?>