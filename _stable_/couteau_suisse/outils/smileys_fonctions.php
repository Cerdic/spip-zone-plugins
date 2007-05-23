<?php

function balise_SMILEYS_dist($p) {
	// le tableau des smileys est present dans les metas
	$smileys = unserialize($GLOBALS['meta']['cs_smileys']);
	$max = count($smileys[1]);
	// le premier argument est le nombre de colonne
	$php = interprete_argument_balise(1, $p);
	if ($php) {
		eval("\$nb_col = intval($php);");
		if ($nb_col<=0) $nb_col = $max;
	} else $nb_col = 8;
	// le second argument est 'titre' si on veut un titre
	$head = interprete_argument_balise(2, $p);
	if ($head) eval("\$head = $head;");
	$titre = _T('cout:smileys_dispos');
	$head = $head=='titre'?"<thead><tr class=\"row_first\"><td colspan=\"$nb_col\">$titre</td></tr></thead>":"";
	$html = "<table summary=\"$titre\" class=\"spip cs_smileys smileys\">$head";
	$l = 1;
	for ($i=0; $i<$max; $i++) {
		//echo $i,':', $i % 5, '<br>';
		if ($i % $nb_col == 0) {
			$class = 'row_'.alterner($l++, 'even', 'odd');
			$html .= "<tr class=\"$class\">";
		}
		$html .= "<td>{$smileys[1][$i]}<br />{$smileys[0][$i]}</td>";
		if ($i % $nb_col == $nb_col - 1)
			$html .= "</tr>\n";
	}
	// on finit la ligne qd meme...
	if ($i = $max % $nb_col) $html .= str_repeat('<td>&nbsp;</td>', $nb_col - $i) . '</tr>';

	// accessibilite : alt et title avec le smiley en texte
	$html = preg_replace('/@@64@@([^@]*)@@65@@/e', "base64_decode('\\1')", $html);
	$html = str_replace("'", "\'", $html);
	$p->code = "'$html\n</table>\n'";
	$p->interdire_scripts = true;
	$p->type = 'html';
	return $p;
}
?>
