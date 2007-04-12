<?php

include_spip('inc/presentation');
include_spip('inc/documents_auteurs');
// La fonction qui en appelle une autre et qui va nous modifier notre base de donnee comme on en a envie...

function documents_auteurs_ajouts($id_auteur)
{
	echo afficher_documents_auteurs_colonne($id_auteur, 'auteur');
}
?>