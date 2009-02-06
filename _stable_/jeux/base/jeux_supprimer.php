<?php
function jeux_supprimer_tout_jeu($id_jeu){
	if(function_exists('sql_delete'))
		sql_delete('spip_jeux_resultats', "id_jeu=$id_jeu");
	else
		spip_query("DELETE FROM spip_jeux_resultats WHERE id_jeu=$id_jeu");
}

function jeux_supprimer_tout_auteur($id_auteur){
	if(function_exists('sql_delete'))
		sql_delete('spip_jeux_resultats', "id_auteur=$id_auteur");
	else
		spip_query("DELETE FROM spip_jeux_resultats WHERE id_auteur=$id_auteur");
}

function jeux_supprimer_tout_tout(){
	if(function_exists('sql_delete'))
		sql_delete('spip_jeux_resultats');
	else
		spip_query('DELETE FROM spip_jeux_resultats');
}
?>