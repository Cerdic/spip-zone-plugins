<?php
// declarer la fonction du pipeline
function ieconfig_autoriser(){}

function autoriser_ieconfig_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', $type, $id, $qui, $opt);
}

function autoriser_bando_ieconfig_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', $type, $id, $qui, $opt);
}

// Seuls les webmestres peuvent utiliser IEconfig
function autoriser_ieconfig_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', $type, $id, $qui, $opt);
}

?>