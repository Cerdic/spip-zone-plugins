<?
function jeux_supprimer_tout_article($id_jeu){
	spip_query('DELETE FROM spip_jeux_resultats WHERE id_jeu='.$id_jeu);
	}
?>