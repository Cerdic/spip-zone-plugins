<?php

// G�n�re la table des mati�res de l'article � partir des intertitres

function dublin_core_tableofcontents ($texte) {
	$table = '';
	$i = 0;
	preg_match_all('#\{\{\{(.+)\}\}\}#U',$texte,$matches);
	foreach ($matches[1] as $titre) {
		if ($i>0)
			$table .= ' -- '.typo($titre);
		else
			$table .= typo($titre);
		$i++;
	}
	return $table;
}
?>