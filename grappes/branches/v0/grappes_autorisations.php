<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function autoriser_grappe(){}

function autoriser_grappe_creer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('modifier', $type, $id, $qui, $opt);
}

function autoriser_grappe_modifier_dist($faire, $type, $id, $qui, $opt){
	$id_admin = sql_getfetsel('id_admin','spip_grappes','id_grappe='.intval($id));
	return ((($qui['statut']=='0minirezo') AND !$qui['restreint']) OR ($qui['id_auteur'] == $id_admin));
}

function autoriser_grappe_associer_dist($faire, $type, $id, $qui, $opt){
	$res = sql_fetsel(array('id_admin','liaisons','options'),'spip_grappes','id_grappe='.sql_quote($id));
	if (!is_array($options = @unserialize($res['options'])))
		$acces = array('0minirezo');
	else {
		$acces = is_array($options['acces'])?$options['acces']:array('0minirezo');
	}
	// tester le statut de l'auteur
	if (!in_array($qui['statut'],$acces) OR ($res['id_admin'] == $qui['id_auteur']))
		return false;

	// tester si l'on a le droit d'ajouter cet objet
	if ($opt['cible']) {
		$liaisons = explode(',',$res['liaisons']);
		if (!in_array(table_objet($opt['cible']),$liaisons))
			return false;
	}

	return true;
}

?>
