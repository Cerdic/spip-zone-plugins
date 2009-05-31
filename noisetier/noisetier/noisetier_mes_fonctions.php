<?php

//Filtres pour le compilateur de page
//Créés les expressions régulières pour la sélection des noisettes
function noisetier_selection_page($texte) {
	$result = '^toutes$';
	$result .= '|^'.$texte.'$';
	$result .= '|^'.$texte.',';
	$result .= '|,'.$texte.'$';
	$result .= '|,'.$texte.',';
	return $result;
}
function noisetier_selection_exclue($texte) {
	$result = '^'.$texte.'$';
	$result .= '|^'.$texte.',';
	$result .= '|,'.$texte.'$';
	$result .= '|,'.$texte.',';
	return $result;
}

?>