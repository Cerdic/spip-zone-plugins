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

// Pipeline gmap_implementations :
// Lister les implémentations disponibles
// Data = array(
// 	'<code-de-l-implementation>' => array(
//		'name'=> texte, nom de l'implémentation, affiché dans la liste de choix de l'implémentation,
//		'explic'=> texte, ligne de description affichée dans la page de configuration))
$GLOBALS['spip_pipeline']['gmap_implementations'] .= '';

// Pipeline gmap_implementations :
// Lister et récupérer le contenu des outils disponibles pour la géolocalisation
// Data = array(
// 	'<code-de-l-outil>' => array(
//		'name'=> texte, nom de l'outil, affiché dans la liste de choix de l'outil,
//		'content'=> html, contenu de la div qui fait l'interface utilisateur (sans le tableau de résultats)))
$GLOBALS['spip_pipeline']['gmap_outils_geoloc'] .= '';

?>