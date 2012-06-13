<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 */

// Autorisation du plugin
function gmap_autoriser()
{
}

// Autorisation de la configuration de GMap, uniquement pour le webmestre
function autoriser_configurer_gmap_bouton_dist($faire, $type, $id, $qui, $opt)
{
	// Seulement si on est admin
	return (autoriser('webmestre'));
} 

?>