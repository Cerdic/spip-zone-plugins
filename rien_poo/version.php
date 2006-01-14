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

$GLOBALS['spip_matrice']['Rien::leFiltre'] =
	dirname(__FILE__).'/Rien.php';

?>
