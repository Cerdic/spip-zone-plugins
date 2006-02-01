<?php

/*
 * ancres_intertitres
 *
 * introduit une ancre translitteree pour les intertitres
 *
 * Auteur : collectif
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */

$nom = 'ancres_intertitres';
$version = 0.1;

// s'inserer dans le pipeline 'avant_propre' @ ecrire/inc_texte.php3
$GLOBALS['spip_pipeline']['pre_propre'] .= '|AncresIntertitres::ancres_intertitres';

$GLOBALS['spip_matrice']['AncresIntertitres::ancres_intertitres'] = dirname(__FILE__).'/ancres_intertitres.php';

?>