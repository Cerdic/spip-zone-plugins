<?php
/**
 * Ce fichier contient l'ensemble des constantes et des utilitaires nécessaires au fonctionnement du plugin.
 *
 * @package SPIP\TAXONOMIE
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/taxonomer');
include_spip('inc/filtres');

function tester_taxon_merger_traductions() {
	$jeu = array(
		array('', '', ''),
		array('', '', "<multi></multi>"),
		array('', '', "<multi> </multi>"),
		array('<multi>[fr]en français</multi>', '', '<multi>[fr]en français</multi>'),
		array('<multi>[fr]en français</multi>', '', '[fr]en français'),
		array('', '<multi></multi>', ''),
		array('', '<multi> </multi>', ''),
		array('<multi>[fr]en français</multi>', '<multi>[fr]en français</multi>', ''),
		array('<multi>[fr]en français</multi>', '[fr]en français', ''),
		array('', '<multi></multi>', "<multi></multi>"),
		array('<multi>[en]in English[fr]en français</multi>', '<multi>[fr]en français</multi>', "<multi>[en]in English</multi>"),
		array('<multi>[en]in English[fr]en français 1</multi>', '<multi>[fr]en français 1</multi>', "<multi>[fr]en français 2[en]in English</multi>"),
		array('<multi>par defaut[fr]en français</multi>', '<multi>[fr]en français</multi>', "<multi>par defaut</multi>"),
		array('<multi>par defaut[fr]en français</multi>', '<multi>par defaut </multi>', "<multi>[fr]en français </multi>"),
	);

	$html = '';
	foreach ($jeu as $_numero => $_multi) {
		$result = $_multi[0];
		$prio = $_multi[1];
		$non_prio = $_multi[2];
		$merge = taxon_merger_traductions($prio, $non_prio);
		$couleur = ($merge == $result) ? 'green' : 'red';
		$html .= "<dt>Cas ${_numero} : prio='<code>${prio}</code>' - non_prio='<code>${non_prio}</code>' - resultat attendu='<code>${result}</code>'</dt>";
		$html .= "<dd style='color:${couleur}'>=> '<code>${merge}</code>'</code></dd>";
	}
	if ($html) {
		$html = "<dl>${html}</dl>";
	}

	return $html;
}

function tester_extraire_multi() {
	$jeu = array(
		0 => array('', ''),
		1 => array('', "<multi></multi>"),
		2 => array(' ', "<multi> </multi>"),
		3 => array('[fr]en français', '<multi>[fr]en français</multi>'),
		4 => array('', '[fr]en français'),
	);

	$html = '';
	foreach ($jeu as $_numero => $_multi) {
		$attendu = $_multi[0];
		$multi = $_multi[1];
		$retour = preg_match(_EXTRAIRE_MULTI, $multi, $match);
		$resultat = !$retour ? '' : $match[1];
		$couleur = ($resultat == $attendu) ? 'green' : 'red';
		$html .= "<dt>Cas multi='<code>${multi}</code>' - resultat attendu='<code>${attendu}</code>'</dt>";
		$html .= "<dd style='color:${couleur}'>=> '<code>${resultat}</code>'</code></dd>";
	}
	if ($html) {
		$html = "<dl>${html}</dl>";
	}

	return $html;
}

?>