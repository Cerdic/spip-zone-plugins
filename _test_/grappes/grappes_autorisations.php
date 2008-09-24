<?php

function autoriser_grappe_creer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('modifier', $type, $id, $qui, $opt);
}

function autoriser_grappe_modifier_dist($faire, $type, $id, $qui, $opt){
	return ($qui['statut']=='0minirezo') AND !$qui['restreint'];
}

function autoriser_grappe_associer_dist($faire, $type, $id, $qui, $opt){
	$options = sql_getfetsel('options','spip_grappes','id_grappe='.sql_quote($id));
	if (!is_array($options = @unserialize($options)))
		$acces = array('0minirezo');
	else {
		$acces = $options['acces'];
	}

	if (!in_array($qui['statut'],$acces)) 
		return false;
	
	return true;
}

?>
