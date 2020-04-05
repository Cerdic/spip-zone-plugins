<?php
function jeux_compacter_tout_jeu($id_jeu){
	include_spip('public/assembler');
	$liste = recuperer_fond('fonds/jeux_compacter', array('id_jeu'=>$id_jeu));

		sql_delete('spip_jeux_resultats', "id_jeu=$id_jeu AND ".sql_in('id_resultat', $liste, 'NOT'));

}

function jeux_compacter_tout_auteur($id_auteur){
	include_spip('public/assembler');
	$liste = recuperer_fond('fonds/jeux_compacter', array('id_auteur'=>$id_auteur));
    sql_delete('spip_jeux_resultats', "id_auteur=$id_auteur AND ".sql_in('id_resultat', $liste, 'NOT'));
}

function jeux_compacter_tout_tout(){
	include_spip('public/assembler');
	$liste = recuperer_fond('fonds/jeux_compacter');
    sql_delete('spip_jeux_resultats', sql_in('id_resultat', $liste, 'NOT'));

}
?>