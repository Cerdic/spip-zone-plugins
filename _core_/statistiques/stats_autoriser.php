<?php

function stats_autoriser(){}


// Lire les stats ?
// = tous les admins
// http://doc.spip.org/@autoriser_voirstats_dist
function autoriser_voirstats_dist($faire, $type, $id, $qui, $opt) {
	return (($GLOBALS['meta']["activer_statistiques"] != 'non')
			AND ($qui['statut'] == '0minirezo'));
}

// autorisation des boutons et onglets
function autoriser_statistiques_visites_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voirstats', $type, $id, $qui, $opt);
}

function autoriser_statistiques_repartition_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voirstats', $type, $id, $qui, $opt);
}

function autoriser_statistiques_lang_bouton_dist($faire, $type, $id, $qui, $opt) {
	return ($GLOBALS['meta']['multi_articles'] == 'oui' 
			OR $GLOBALS['meta']['multi_rubriques'] == 'oui')
		AND autoriser('voirstats', $type, $id, $qui, $opt);
}

function autoriser_statistiques_referers_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voirstats', $type, $id, $qui, $opt);
}


?>
