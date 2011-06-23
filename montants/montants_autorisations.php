<?php

function montants_autoriser() {}


/*function autoriser_montants_dist($faire, $type, $id, $qui, $opt) {
	switch ($faire) {
		case 'onglet':
		case 'configurer':
		case 'editer':
			return ($qui['statut'] == '0minirezo');
			break;
		default:
			return false;
			break;
	}
}*/

function autoriser_montants_creer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('modifier', $type, $id, $qui, $opt);
}

function autoriser_montants_modifier_dist($faire, $type, $id, $qui, $opt){
	return ($qui['statut']=='0minirezo') AND !$qui['restreint'];
}


?>
