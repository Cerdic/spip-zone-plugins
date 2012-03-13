<?php

// Syncronisations des liste
function genie_syncro_listes_dist ($t) {
	
	// Définit le fuseau horaire par défaut à utiliser, GMT est le fuseau utilisé par mailchimp.
	date_default_timezone_set('GMT');
		
	include_spip('sclp_fonctions');
	$listes_accordes=lire_config('squirrel_chimp/mailinglists');

	$listes=array_keys($listes_accordes);
	
	$frequence_actualisation=1;
	

	
	

	$id_liste=sql_getfetsel('id_liste','spip_listes','statut!="poubelle" AND id_liste IN ('.implode(',',$listes).')','','date_syncro');
	
	$today=date('Y-m-d G:i:s');	
	
	if($id_liste)$derniere_syncro=sql_getfetsel('date_syncro','spip_listes_syncro','objet="listes" AND type_syncro="liste" AND id_objet='.$id_liste,'','date_syncro DESC');
	
	$return=0;
	
	if($id_liste){
		if($derniere_syncro){
			$d1 = new DateTime($today); 
			$d2 = new DateTime($derniere_syncro); 
			$diff = $d1->diff($d2); 

			$intervale = $diff->h;
			
			$difference= $diff->h.':'.$diff->i.':'.$diff->s; 
			
			//actualisation chaque heure			
			if($intervale>=$frequence_actualisation){
				$syncroniser=charger_fonction('syncroniser_listes','inc');
				$resultat = $syncroniser('',$id_liste,$listes_accordes[$id_liste],$status,$start,$limit,false);	
				$return=1;
				}
			}
		else{
			$syncroniser=charger_fonction('syncroniser_listes','inc');
			$resultat = $syncroniser('',$id_liste,$listes_accordes[$id_liste],$status,$start,$limit,true);
			$interval='première syncro';
			$return=1;		
			}
		}
	
	spip_log('action cron syncro date: '.$today. ' dernière syncro de la liste '.$id_liste.' : '.$derniere_syncro.' différence : ' .$difference.' h','sclp');
    return $return;
}
?>
