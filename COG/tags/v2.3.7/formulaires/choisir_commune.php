<?php

// https://code.spip.net/@inc_editer_mot_dist
function formulaires_choisir_commune_charger($type,$id){


$tab_commune=sql_allfetsel('id_cog_commune','spip_cog_communes_liens','id_objet='.$id.' AND objet='.sql_quote($type));
foreach($tab_commune as &$commune)
{
	$commune=$commune['id_cog_commune'];
}

	$valeurs=array(
					'type'  => $type,
					'id'	=> $id,
					'id_cog_commune[]' 	=> array(),
					'tab_commune'=>$tab_commune
					);
	return $valeurs;

}



function formulaires_choisir_commune_verifier_dist($type,$id){

	$erreurs = array();
	return $erreurs;
}

// https://code.spip.net/@inc_editer_mot_dist
function formulaires_choisir_commune_traiter_dist($type,$id){
	include_spip('cog_fonctions');
	$table='spip_cog_communes_liens';
	$tab_id_cog_commune=_request('id_cog_commune');
	if(empty($tab_id_cog_commune))
		{
		$tab_nom_commune=explode(',',_request('nom_commune'));
		foreach($tab_nom_commune as $nom_commune){
			$commune=cog_recherche_commune_strict($nom_commune);
			if(!empty($commune)){
				$tab_id_cog_commune[] = $commune['id_cog_commune'];
				}
			}
		}

	if(!empty($tab_id_cog_commune))
		{
		foreach($tab_id_cog_commune as $id_cog_commune)
			{
			if($id_cog_commune!=0){
			$r = sql_countsel(array('cl'=>$table,'c'=>'spip_cog_communes'), 'cl.id_cog_commune=c.id_cog_commune and c.id_cog_commune='.$id_cog_commune.' AND id_objet='.$id.' AND objet='.sql_quote($type));
			if ($r==0)
				sql_insertq($table, array('id_cog_commune' =>$id_cog_commune,'objet'=> $type,  'id_objet' => $id));
			}
			}
		}

	return $message;
}
?>