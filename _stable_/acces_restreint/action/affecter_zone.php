<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */


function action_affecter_zone_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();
	
	if (preg_match(',^([0-9]+|-1)-([a-z]+)-([0-9]+|-1)$,',$arg,$regs)
	  AND $regs[2]=='auteur')	{
		$id_zone = intval($regs[1]);
		$id_auteur = intval($regs[3]);
		include_spip('action/editer_zone');
		if ($id_auteur==-1)
			$id_auteur = array_map('reset',sql_allfetsel('id_auteur','spip_auteurs',"statut!='poub'"));
		accesrestreint_revision_zone_objets_lies($id_zone=='-1'?'':$id_zone,$id_auteur,'auteur');
	}
}

?>