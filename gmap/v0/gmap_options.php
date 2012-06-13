<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 */

// Pipeline gmap_implementations :
// Lister les impl�mentations disponibles
// Data = array(
// 	'<code-de-l-implementation>' => array(
//		'name'=> texte, nom de l'impl�mentation, affich� dans la liste de choix de l'impl�mentation,
//		'explic'=> texte, ligne de description affich�e dans la page de configuration))
$GLOBALS['spip_pipeline']['gmap_implementations'] .= '';

// Pipeline gmap_implementations :
// Lister et r�cup�rer le contenu des outils disponibles pour la g�olocalisation
// Data = array(
// 	'<code-de-l-outil>' => array(
//		'name'=> texte, nom de l'outil, affich� dans la liste de choix de l'outil,
//		'content'=> html, contenu de la div qui fait l'interface utilisateur (sans le tableau de r�sultats)))
$GLOBALS['spip_pipeline']['gmap_outils_geoloc'] .= '';

?>