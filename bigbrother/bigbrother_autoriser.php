<?php

function bigbrother_autoriser(){}

/**
 * Autorisation à supprimer des données du journal
 * Retourne par défaut les mêmes droits qu'un webmestre
 *
 * @param unknown_type $faire
 * @param unknown_type $type
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $opt
 */
function autoriser_journal_supprimer_dist($faire,$type,$id,$qui,$opt){
	return autoriser('webmestre', $type, $id, $qui, $opt);
}

?>