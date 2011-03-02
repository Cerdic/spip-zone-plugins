<?php
function inc_actualiser_dist($id){

	$t=sql_select('montant_reel,duree_reelle','spip_projets_taches','statut != "50poubelle" AND id_projet='.sql_quote($id));
		
	$compteur=array();
	while($data = sql_fetch($t)){
		foreach($data AS $champ=>$valeur){
			$compteur[$champ][]=$valeur;
			}
		}

	if(is_array($compteur['montant_reel']))$montant_reel=array_sum($compteur['montant_reel']);
	if(is_array($compteur['duree_reelle']))$duree_reelle=array_sum($compteur['duree_reelle']);		
	
	$val_projet=array(
		'montant_reel'=>$montant_reel,
		'duree_reelle'=>$duree_reelle,			
		);
		
	sql_updateq('spip_projets',$val_projet,'id_projet='.sql_quote($id)); 
	
return;

 }
?>