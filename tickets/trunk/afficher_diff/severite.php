<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2013
 *
 * @package SPIP\Tickets\Diff
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/diff');

/**
 * Afficher le diff du champ sévérité
 * 
 * @param string $champ
 * @param string $old
 * @param string $new
 * @param string $format
 *   apercu, diff ou complet
 * @return string
 */
function afficher_diff_severite_dist($champ,$old,$new,$format='diff'){
	// ne pas se compliquer la vie !
	if ($old==$new)
		$out = ($format!='complet'?'':tickets_texte_severite($new));
	else {
		$diff = new Diff(new DiffTexte);
		$n = preparer_diff(tickets_texte_severite($new));
		$o = preparer_diff(tickets_texte_severite($old));

		$out = afficher_diff($diff->comparer($n,$o));
		if ($format == 'diff' OR $format == 'apercu')
			$out = afficher_para_modifies($out, ($format == 'apercu'));
	}
	return $out;
}
?>