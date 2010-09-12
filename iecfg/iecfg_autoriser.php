<?php
// declarer la fonction du pipeline
function iecfg_autoriser(){}

function autoriser_iecfg_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', $type, $id, $qui, $opt);
}

function autoriser_bando_iecfg_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', $type, $id, $qui, $opt);
}

?>