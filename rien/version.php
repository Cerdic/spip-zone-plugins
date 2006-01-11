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

$nom = 'rien';
$version = 0.1;

// s'inserer dans le pipeline 'avant_propre' @ ecrire/inc_texte.php3
$GLOBALS['spip_pipeline']['post_propre'][] = 'le_filtre_rien';

$GLOBALS['spip_matrice']['le_filtre_rien'] =
	dirname(__FILE__).'/definition_de_rien.php';

?>
