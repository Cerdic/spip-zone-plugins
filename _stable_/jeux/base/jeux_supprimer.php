<?php
function jeux_supprimer_tout_jeu($id_jeu){
	spip_query('DELETE FROM spip_jeux_resultats WHERE id_jeu='.$id_jeu);
	}
function jeux_supprimer_tout_auteur($id_auteur){
	spip_query('DELETE FROM spip_jeux_resultats WHERE id_auteur='.$id_auteur);
	}
function jeux_supprimer_tout_tout(){
	spip_query('DELETE FROM spip_jeux_resultats');
	}
?>