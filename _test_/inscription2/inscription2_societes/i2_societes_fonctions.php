<?php
function id_societe_to_societe($id_societe){
	if(intval($id_societe) && ($id_societe != 0)){
		$societe = sql_getfetsel('nom', 'spip_societes', 'id_societe ='.intval($id_societe));
		return $societe;
	}else if(is_array(unserialize($id_societe))){
		return id_societes_to_societes(unserialize($id_societe));
	}
	else return;
}

function id_societes_to_societes($id_societes){
	if(is_array($id_societes)){
		$societes = implode(',',$id_societes);
		$societes = sql_select('nom', 'spip_societes', "id_societe IN ($societes)");
		$ret = '';
		while($societe = sql_fetch($societes)){
			$ret .= $societe['nom'].', ';
		}
		$ret = substr($ret,0,-2);
		return $ret;
	}
	else return;
}

?>
