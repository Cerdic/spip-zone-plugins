<?php

/*
 * ancres
 *
 * introduit le raccourci [#ancre<-] pour les ancres
 *
 * Auteur : collectif
 * © 2005 - Distribue sous licence BSD
 *
 */

$nom = 'ancres';
$version = 0.1;

// s'inserer dans le pipeline 'avant_propre' @ ecrire/inc_texte.php3
$GLOBALS['spip_pipeline']['post_propre'] .= '|ancres';

$GLOBALS['spip_matrice']['ancres'] =
	dirname(__FILE__).'/ancres.php';

?>