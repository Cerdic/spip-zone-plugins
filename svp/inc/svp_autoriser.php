<?php
/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function svp_autoriser(){}

/**
 * Autorisation d'iconification d'un depot
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_depot_iconifier_dist($faire, $type, $id, $qui, $opt){
	return true;
}

?>
