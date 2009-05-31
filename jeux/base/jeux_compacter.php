<?php
function jeux_compacter_tout_jeu($id_jeu){
	include_spip('public/assembler');
	$liste = recuperer_fond('fonds/jeux_compacter', array('id_jeu'=>$id_jeu));
	if(defined('_SPIP19300'))
		sql_delete('spip_jeux_resultats', "id_jeu=$id_jeu AND ".sql_in('id_resultat', $liste, 'NOT'));
	else
		spip_query("DELETE FROM spip_jeux_resultats WHERE id_jeu=$id_jeu AND id_resultat NOT IN ($liste)");
}

function jeux_compacter_tout_auteur($id_auteur){
	include_spip('public/assembler');
	$liste = recuperer_fond('fonds/jeux_compacter', array('id_auteur'=>$id_auteur));
	if(defined('_SPIP19300'))
		sql_delete('spip_jeux_resultats', "id_auteur=$id_auteur AND ".sql_in('id_resultat', $liste, 'NOT'));
	else
		spip_query("DELETE FROM spip_jeux_resultats WHERE id_auteur=$id_auteur AND id_resultat NOT IN ($liste)");	
}

function jeux_compacter_tout_tout(){
	include_spip('public/assembler');
	$liste = recuperer_fond('fonds/jeux_compacter');
	if(defined('_SPIP19300'))
		sql_delete('spip_jeux_resultats', sql_in('id_resultat', $liste, 'NOT'));
	else
		spip_query("DELETE FROM spip_jeux_resultats WHERE id_resultat NOT IN ($liste)");
}
?>