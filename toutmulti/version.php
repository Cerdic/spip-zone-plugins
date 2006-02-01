<?php

/*
 * toutmulti
 *
 * introduit le raccourci <:texte:> pour introduire librement des
 * blocs multi dans un flux de texte (via typo ou propre)
 *
 * Auteur : collectif
 * © 2006 - Distribue sous licence BSD
 *
 */

$nom = 'toutmulti';
$version = 0.1;

// s'inserer dans le pipeline 'avant_propre' @ ecrire/inc_texte.php3
$GLOBALS['spip_pipeline']['pre_typo'] .= '|ToutMulti::toutmulti';

$GLOBALS['spip_matrice']['ToutMulti::toutmulti'] = dirname(__FILE__).'/toutmulti.php';

?>