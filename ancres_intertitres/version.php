<?php

/*
 * ancres_intertitres
 *
 * introduit une ancre translittérée pour les intertitres
 *
 * Auteur : collectif
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */

$nom = 'ancres_intertitres';
$version = 0.1;

// s'inserer dans le pipeline 'avant_propre' @ ecrire/inc_texte.php3
$GLOBALS['spip_pipeline']['pre_propre'] .= '|ancres_intertitres';

$GLOBALS['spip_matrice']['ancres_intertitres'] = dirname(__FILE__).'/ancres_intertitres.php';

?>
