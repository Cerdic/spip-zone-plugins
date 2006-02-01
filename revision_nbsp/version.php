<?php

/*
 * revision_nbsp
 *
 * Dans l'espace prive, souligne en grise les espaces insecables
 *
 * Auteur : fil@rezo.net
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */

$nom = 'revision_nbsp';
$version = 0.1;

// s'inserer dans le pipeline 'apres_typo' @ ecrire/inc_texte.php3
if (!_DIR_RESTREINT)
	$GLOBALS['spip_pipeline']['post_typo'] .= '|RevisionNbsp::revision_nbsp';

$GLOBALS['spip_matrice']['RevisionNbsp::revision_nbsp'] = dirname(__FILE__).'/revision_nbsp.php';

?>