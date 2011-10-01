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

// Il est nécessaire d'interdire le compactage des JavaScripts dans la partie privée
// parce que l'API Google n'est pas compactée alors que d'autres fichiers le sont et 
// se retrouvent donc avant elle dans le fichier final !
define('_INTERDIRE_COMPACTE_HEAD_ECRIRE',true);

// Ajouter un pipeline pour récupérer l'info exif/iptc
$GLOBALS['spip_pipeline']['gmap_implementations'] .= '';

?>