<?php

/*
 * smallcaps
 *
 * introduit le raccourci <sc>...</sc> pour les petites majuscules
 *
 * Auteur : arno@scarabee.com
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */

$nom = 'smallcaps';
$version = 0.1;

// s'inserer dans le pipeline 'apres_typo' @ ecrire/inc_texte.php3
$GLOBALS['spip_pipeline']['post_typo'] .= '|smallcaps';

$GLOBALS['spip_matrice']['smallcaps'] = dirname(__FILE__).'/smallcaps.php';

?>