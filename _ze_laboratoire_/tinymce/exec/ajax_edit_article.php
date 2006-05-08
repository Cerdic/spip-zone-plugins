<?php

error_log("REQUEST :".var_export($_REQUEST, 1)."\n");
error_log("GET :".var_export($_GET, 1)."\n");
error_log("POST :".var_export($_POST, 1)."\n");

// permet de modifier un element d'article depuis un appel ajax
// A FAIRE : generer l'update qui va bien
function exec_ajax_edit_article_dist() {
	$id= _request('id');
	$champ= _request('champ');
	$texte= _request('texte');

	error_log("MODIF DE $id:$champ => $texte");

	echo "==> ".$texte;
}

?>
