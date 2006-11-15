<?php
/*
 * SPIPmotion
 * Gestion de l'encodage des videos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet
 * 2006 - Distribue sous licence GNU/GPL
 *
 */

function spipmotion_afficher_insertion_videos($id_article) {
	global $connect_id_auteur, $connect_statut;

	$s = "";
	// Ajouter le formulaire d'ajout de videos
	$s .= "\n<p>";
	$s .= debut_cadre_relief();
	$s .= "<a name='videos'>Videos</a>\n";
	$s .= fin_cadre_relief(true);
	return $s;
}

?>