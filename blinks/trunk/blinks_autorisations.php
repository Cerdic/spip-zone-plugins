<?php
	if (!defined("_ECRIRE_INC_VERSION")) return;
	// fonction pour le pipeline, n'a rien a effectuer
	function blinks_autoriser(){}
	// declarations d'autorisations
	function autoriser_blinks_bouton_dist($faire, $type, $id, $qui, $opt) {
		return autoriser('voir', 'blinks', $id, $qui, $opt);
	}
	function autoriser_blinks_voir_dist($faire, $type, $id, $qui, $opt) {
		return true;
	}
	function autoriser_blink_voir_dist($faire, $type, $id, $qui, $opt) {
		return autoriser('modifier', 'blink', $id, $qui, $opt);
	}
	function autoriser_blink_modifier_dist($faire, $type, $id, $qui, $opt) {
		return in_array($qui['statut'], array('0minirezo', '1comite'));
	}	
?>