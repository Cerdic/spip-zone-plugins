<?php
/**
 * Plugin Spipmine pour Spip 2.0
 * Licence GPL (c) 2009 - 2010
 * Auteur Cyril MARION - Ateliers CYM
 *
 */

function colorscope_post_typo($texte) {
	$motif = '`(#[a-fA-F0-9]{6})`';
	$texte = preg_replace($motif, '<span style="display:inline-block;text-align:center;width:90px;height:20px;background-color:$1;color:white">$1</span>', $texte);
	return $texte;
}

?>