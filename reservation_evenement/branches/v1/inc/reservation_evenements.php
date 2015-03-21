<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

//Teste si on est en présence d'une zone reservation événement
function rubrique_reservation($id,$objet,$rubrique_reservation=''){
	//On récupère la config si pas passé comme variable
	if(!$rubrique_reservation){
		include_spip('inc/config');
		include_spip('formulaires/selecteur/generique_fonctions');
		$config=lire_config('reservation_evenement/',array());
		$rubrique_reservation=isset($config['rubrique_reservation'])?picker_selected($config['rubrique_reservation'],'rubrique'):'';
	}
	
	//Si une zone a  été définit
	if(is_array($rubrique_reservation) and count($rubrique_reservation)!=0){
		//On établit les id selon l'objet
		if($objet=='article'){
			$sql=sql_select('id_article','spip_articles','statut="publie" AND id_rubrique IN ('.implode($rubrique_reservation).')');
		}
		elseif($objet=='evenement'){
			$sql=sql_select('e.id_evenement','spip_evenements AS e INNER JOIN spip_articles AS a ON e.id_article=a.id_article','e.id_evenement_source=0 AND a.statut="publie" AND e.statut="publie" AND a.id_rubrique IN ('.implode(',',$rubrique_reservation).')');
		}
		
		$ids=array();
		if($sql){
			while($data=sql_fetch($sql)){
				if(!in_array($data['id_'.$objet],$ids))$ids[]=$data['id_'.$objet];
			}				
		}
		if(in_array($id,$ids))return true; //Si l'id courent est compris dans les ids dce l'objet on affiche
		else return false;//Sinon on n'affice pas
	}
	else return true; //Si pas de zone on affiche tout
}