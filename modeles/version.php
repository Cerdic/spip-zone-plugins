<?php

/*
 * ancres
 *
 * introduit le raccourci <breve12|squelette> pour les modeles
 *
 * Auteur : Fil & ...
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

$nom = 'modeles';
$version = 0.1;

// s'inserer dans le pipeline 'pre_propre' @ ecrire/inc_texte.php3
$GLOBALS['spip_pipeline']['pre_propre'] .= '|Modeles::traiter_modeles';

$GLOBALS['spip_matrice']['Modeles::traiter_modeles'] = dirname(__FILE__).'/modeles.php';

?>