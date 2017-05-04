<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Lister les types de lignes possibles
 * @return array
 */
function storify_types() {
	$type = array(
		'center' => 1,
		'two_rows' => 2,
		'two_rows_alt' => 2,
		'two_rows_large' => 2,
		'two_rows_large_alt' => 2,
		'photo_large' => 2,
		'stats' => 9,
		'action' => 3,
	);

	return $type;
}

/**
 * Ajouter un marqueur au texte pour indiquer que c'est une story
 * @param $texte
 * @return string
 */
function storify_texte_to_story($texte) {
	return "<!--story-->" . $texte;
}

/**
 * Decouper le texte en blocs (lignes + colonnes) d'histoire
 * @param string $texte
 * @param bool $edit
 * @param bool $storify
 * @return array
 */
function storify_from_texte($texte, $edit = false, $storify = null) {
	$texte = ltrim($texte);

	$valeurs = array(
		'_storified' => false,
		'storify' => false,
		'story_lines' => array(),
	);
	$empty_line = array(
		'type'=>'',
		'blocks'=>array(),
	);

	if (strncmp($texte, '<!--story-->', 12) == 0) {
		$texte = ltrim(substr($texte, 12));
		if (is_null($storify)) {
			$storify = true;
		}
	}
	if ($storify) {
		$valeurs['storify'] = $valeurs['_storified'] = true;

		// decouper la story

		$parts = preg_split(",(<!--story:\w+:L\d+:R\d+:-->),", $texte, -1, PREG_SPLIT_DELIM_CAPTURE);
		$story_lines = array();

		$first = array_shift($parts);
		while(count($parts)) {
			$sep = array_shift($parts);
			$t = array_shift($parts);
			if ($first) {
				$t = $first . $t;
				$first = '';
			}
			$sep = explode(':', $sep);
			$type = $sep[1];
			$line = ltrim($sep[2],'L');
			$row = ltrim($sep[3],'R');

			if (!isset($story_lines[$line])) {
				$story_lines[$line] = $empty_line;
			}
			if (!$story_lines[$line]['type']) {
				$story_lines[$line]['type'] = $type;
			}
			$story_lines[$line]['blocks'][$row] = trim($t);
		}

		// si aucun bloc mais qu'on a un texte, c'est une premiere fois, il faut retrouver son texte dans la premiere ligne
		if ($first) {
			$story_lines[] = array('blocks'=>array(0=>$first));
		}

		if ($edit) {
			$story_lines[] = $empty_line;
		}

		$valeurs['story_lines'] = storify_valide_story($story_lines);
	}
	return $valeurs;
}

/**
 * Serialize la story dans le texte de l'article
 * @param array $story_lines
 * @return string
 */
function storify_story_to_texte($story_lines) {
	$story_lines = storify_valide_story($story_lines, true);
	$texte = "<!--story-->";
	foreach ($story_lines as $k=>$line) {
		$type = $line['type'];
		foreach ($line['blocks'] as $i=>$block) {
			$texte .= "<!--story:$type:L$k:R$i:-->\n" . rtrim($block)."\n\n";
		}
	}
	return $texte;
}

/**
 * Verifie/complete/nettoie le tableau des lignes de l'histoire
 * @param array $story_lines
 * @param bool $delete_empty
 * @return array
 */
function storify_valide_story($story_lines, $delete_empty = false) {
	$types = storify_types();

	foreach ($story_lines as $k=>$line) {
		if (!isset($line['type']) or !$line['type'] or !isset($types[$line['type']])) {
			$story_lines[$k]['type'] = $line['type'] = 'two_rows';
		}
		if (!isset($line['blocks'])) {
			$story_lines[$k]['blocks'] = $line['blocks'] = array();
		}
		$type = $line['type'];
		$nb_cols = $types[$type];
		// creer les colonnes manquantes si besoin
		for($i=0;$i<$nb_cols;$i++) {
			if (!isset($line['blocks'][$i])) {
				$story_lines[$k]['blocks'][$i] = '';
			}
		}
		// aggreger les colonnes en trop dans la derniere
		foreach ($line['blocks'] as $i=>$t) {
			if ($i>=$nb_cols) {
				$story_lines[$k]['blocks'][$nb_cols-1] .= "\n\n" . $t;
				unset($story_lines[$k]['blocks'][$i]);
			}
		}
		if ($delete_empty and !strlen(trim(implode('',$line['blocks'])))) {
			unset($story_lines[$k]);
		}
	}

	$story_lines = array_values($story_lines);

	return $story_lines;
}

/**
 * Remonter la ligne d'indice $k d'un cran
 * @param int $k
 * @param array $story_lines
 * @return array
 */
function storify_up_line($k, $story_lines) {
	$lines = array();
	$prev = false;
	foreach ($story_lines as $i=>$line) {
		if ($i == $k and count($lines)) {
			$prev = array_pop($lines);
		}
		$lines[] = $line;
		if ($prev) {
			$lines[] = $prev;
			$prev = false;
		}
	}
	return $lines;
}

/**
 * Remonter la ligne d'indice $k d'un cran
 * @param int $k
 * @param array $story_lines
 * @return array
 */
function storify_down_line($k, $story_lines) {
	$lines = array();
	$prev = false;
	foreach ($story_lines as $i=>$line) {
		if ($i == $k) {
			$prev = $line;
		}
		else {
			$lines[] = $line;
			if ($prev) {
				$lines[] = $prev;
				$prev = false;
			}
		}
	}
	if ($prev) {
		$lines[] = $prev;
	}

	return $lines;
}