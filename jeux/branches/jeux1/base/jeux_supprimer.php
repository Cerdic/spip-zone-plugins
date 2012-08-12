<?php
function jeux_supprimer_tout_jeu($id_jeu){
	if(defined('_SPIP19300'))
		sql_delete('spip_jeux_resultats', "id_jeu=$id_jeu");
	else
		spip_query("DELETE FROM spip_jeux_resultats WHERE id_jeu=$id_jeu");
}

function jeux_supprimer_tout_auteur($id_auteur){
	if(defined('_SPIP19300'))
		sql_delete('spip_jeux_resultats', "id_auteur=$id_auteur");
	else
		spip_query("DELETE FROM spip_jeux_resultats WHERE id_auteur=$id_auteur");
}

function jeux_supprimer_tout_tout(){
	if(defined('_SPIP19300'))
		sql_delete('spip_jeux_resultats');
	else
		spip_query('DELETE FROM spip_jeux_resultats');
}
?>