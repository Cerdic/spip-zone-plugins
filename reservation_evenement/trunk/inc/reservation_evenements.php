<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

//Teste si l'objet est dans la zone Réservation Evenement
function rubrique_reservation($id,$objet,$rubrique_reservation=''){
	//On récupère la config si pas passé comme variable
	if(!$rubrique_reservation){
		include_spip('inc/config');
		include_spip('formulaires/selecteur/generique_fonctions');
		$config=lire_config('reservation_evenement/',array());
		$rubrique_reservation=isset($config['rubrique_reservation'])?picker_selected($config['rubrique_reservation'],'rubrique'):'';
	}
	
	//Si une zone a été définit
	if(is_array($rubrique_reservation) and count($rubrique_reservation)!=0){
		//On teste si l'objet se trouve dans la zone
		if($objet=='article'){
			$i=sql_getfetsel('id_article','spip_articles','id_article='.$id.' AND id_rubrique IN ('.implode(',',$rubrique_reservation).')');
		}
		elseif($objet=='evenement'){
			$i=sql_getfetsel('e.id_evenement','spip_evenements AS e INNER JOIN spip_articles AS a ON e.id_article=a.id_article','e.id_evenement_source=0 AND a.statut="publie" AND e.id_evenementt='.$id.' AND a.id_rubrique IN ('.implode(',',$rubrique_reservation).')');
		}
		if($i)return true; //Objet se trouve dans la zone
		else return false;//Objet ne se trouve pas dans la zone
	}
	else return true; //Pas de zone définit
}