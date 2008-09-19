<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */


function action_affecter_zone_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();
	
	if (preg_match(',^([0-9]+|-1)-([a-z]+)-([0-9]+)$,',$arg,$regs)
	  AND $regs[2]=='auteur')	{
		$id_zone = intval($regs[1]);
		$id_auteur = intval($regs[3]);
		AccesRestreint_affecter_auteur_zones($id_auteur,$id_zone=='-1'?'':$id_zone);
	}
}

function AccesRestreint_affecter_auteur_zones($id_auteur,$zones){
	$in = "";
	if ($zones){
		$in = sql_in('id_zone',$zones);
	}
	$liste = sql_allfetsel('id_zone','spip_zones',$in);
	$deja = array_map('reset',sql_allfetsel('id_zone','spip_zones_auteurs',"id_auteur=".intval($id_auteur)));
	foreach($liste as $row){
		if (autoriser('affecterzone','auteur',$id_auteur,null,array('id_zone'=>$row['id_zone']))
		  AND !in_array($row['id_zone'],$deja))
			sql_insertq('spip_zones_auteurs',array('id_zone'=>$row['id_zone'],'id_auteur'=>intval($id_auteur)));
	}
}
?>