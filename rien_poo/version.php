<?php

/*
 * rien
 *
 * plugin vide
 *
 * Auteur : VOUS !
 * © 2006 - Distribue sous licence [BSD / GNU GPL etc]
 *
 */

$nom = 'rien_poo';
$version = 0.1;

// s'inserer dans le pipeline 'avant_propre' @ ecrire/inc_texte.php3
$GLOBALS['spip_pipeline']['post_propre'] .= '|Rien::leFiltre';
$GLOBALS['spip_pipeline']['ajouter_boutons'] .= '|Rien::ajouterBoutons';
$GLOBALS['spip_pipeline']['ajouter_onglets'] .= '|Rien::ajouterOnglets';

$GLOBALS['spip_matrice']['Rien::leFiltre'] =
$GLOBALS['spip_matrice']['Rien::ajouterBoutons'] =
$GLOBALS['spip_matrice']['Rien::ajouterOnglets'] =
	dirname(__FILE__).'/Rien.php';

?>