<?php

// fonction qui renvoie un tableau de smileys uniques
function smileys_uniques($smileys) {
	$max = count($smileys[1]);
	$new = array(array(), array(), array());
	for ($i=0; $i<$max; $i++) {
		if(!in_array($smileys[2][$i], $new[2])) {
			$new[0][] = $smileys[0][$i]; // texte
			$new[1][] = $smileys[1][$i]; // image
			$new[2][] = $smileys[2][$i]; // nom de fichier
		}
	}
	return $new;
}

function balise_SMILEYS_dist($p) {
	// le tableau des smileys est present dans les metas
	$smileys = unserialize($GLOBALS['meta']['cs_smileys']);
	// valeurs par defaut
	$nb_col = 8;
	$titre = _T('cout:smileys_dispos');
	$head = '';
	$liens = false;
	// traitement des arguments : [(#SMILEYS{arg1, arg2, ...})]
	$n=1;
	$arg = interprete_argument_balise($n++,$p);
	while ($arg){
		// un nombre est le nombre de colonne
		if (preg_match(",'([0-9]+)',", $arg, $reg)) 
			$nb_col = intval($reg[1]);
		// on veut un titre
		elseif ($arg=="'titre'") 
			$head = "<thead><tr class=\"row_first\"><td colspan=\"$nb_col\">$titre</td></tr></thead>";
		// on veut un lien d'insertion sur chaque smiley
		elseif ($arg=="'liens'") {
			$liens = true; 
			$smileys = smileys_uniques($smileys);
		}
		$arg = interprete_argument_balise($n++,$p);
	}
	$max = count($smileys[0]);
	if (!$nb_col) $nb_col = $max;
	$html = "<table summary=\"$titre\" class=\"spip cs_smileys smileys\">$head";
	$l = 1;
	for ($i=0; $i<$max; $i++) {
		//echo $i,':', $i % 5, '<br>';
		if ($i % $nb_col == 0) {
			$class = 'row_'.alterner($l++, 'even', 'odd');
			$html .= "<tr class=\"$class\">";
		}
/*
<a onmouseout="helpline('Utilisez les raccourcis typographiques pour enrichir votre mise en page', document.getElementById('barre_1'))" onmouseover="helpline('Insérer un E accent aigu majuscule',document.getElementById('barre_1'))" title="Insérer un E accent aigu majuscule" tabindex="1000" class="spip_barre" href="javascript:barre_inserer('\u00c9',document.getElementById('textarea_1'))"><img alt="Insérer un E accent aigu majuscule" style="height: 16px; width: 16px; background-position: center;" src="dist/icones_barre/eacute-maj.png"/></a>
*/
		$html .= $liens
			?"<td><a href=\"javascript:barre_inserer('{$smileys[0][$i]}',document.getElementById('textarea_1'))\">{$smileys[1][$i]}</a></td>"
			:"<td>{$smileys[1][$i]}<br />{$smileys[0][$i]}</td>";
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
