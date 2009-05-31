<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


include_spip('inc/filtres');
include_spip('inc/messages');

/**
 * Verification de la saisie de #FORMULAIRE_RECOMMANDER
 *
 * @return unknown
 */
function formulaires_recommander_verifier_dist(){
	return messagerie_verifier(array());
}

?>