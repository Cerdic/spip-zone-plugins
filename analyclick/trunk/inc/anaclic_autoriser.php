<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GPL.
*
* Autorisation des boutons
*
**/

function anaclic_autoriser(){}

/** Affichage des statistiques */
function autoriser_statistiques_anaclic_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	// Les mêmes que le plugins de stats
	return autoriser('voirstats', $type, $id, $qui, $opt);
}

?>
