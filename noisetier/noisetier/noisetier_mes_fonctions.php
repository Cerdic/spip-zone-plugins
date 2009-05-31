<?php

//Filtres pour le compilateur de page
//Cr��s les expressions r�guli�res pour la s�lection des noisettes
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