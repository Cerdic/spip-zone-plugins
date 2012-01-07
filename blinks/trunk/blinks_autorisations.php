<?php
	if (!defined("_ECRIRE_INC_VERSION")) return;
	// fonction pour le pipeline, n'a rien a effectuer
	function blinks_autoriser(){}
	// declarations d'autorisations
	function autoriser_lien_bouton_dist($faire, $type, $id, $qui, $opt) {
		return autoriser('voir', 'lien', $id, $qui, $opt);
	}
	function autoriser_lien_voir_dist($faire, $type, $id, $qui, $opt) {
		return true;
	}
	function autoriser_lien_voir_dist($faire, $type, $id, $qui, $opt) {
		return autoriser('modifier', 'lien', $id, $qui, $opt);
	}
	function autoriser_lien_modifier_dist($faire, $type, $id, $qui, $opt) {
		return in_array($qui['statut'], array('0minirezo', '1comite'));
	}	
	
?>