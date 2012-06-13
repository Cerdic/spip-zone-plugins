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

// Pour le chargement du fichier en pipeline
function gmap_autoriser()
{
}

// Autorisation de la configuration de GMap, uniquement pour le webmestre
function autoriser_configurer_gmap_dist($faire, $type, $id, $qui, $opt)
{
	return autoriser('webmestre') || autoriser('0minirezo');
} 
function autoriser_configurer_gmap_bouton_dist($faire, $type, $id, $qui, $opt)
{
	return autoriser('webmestre') || autoriser('0minirezo');
} 

?>