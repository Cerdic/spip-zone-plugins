<?php

// permet de modifier un element d'article depuis un appel ajax
// A FAIRE : generer l'update qui va bien
function exec_ajax_edit_article_dist() {
	$id= $_REQUEST['id'];
	$champ= $_REQUEST['champ'];
	$texte= $_REQUEST['texte'];
	error_log("MODIF DE $id:$champ => $texte");

	echo $texte;
}
