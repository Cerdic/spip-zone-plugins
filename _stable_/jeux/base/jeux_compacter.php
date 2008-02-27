<?php
function jeux_compacter_tout_jeu($id_jeu){
	include_spip('public/assembler');
	$liste = recuperer_fond('fonds/jeux_compacter',array('id_jeu'=>$id_jeu));
	spip_query('DELETE FROM spip_jeux_resultats WHERE  id_resultat NOT IN '.$liste.' and id_jeu='.$id_jeu);
	}
function jeux_compacter_tout_auteur($id_auteur){
	include_spip('public/assembler');
	$liste = recuperer_fond('fonds/jeux_compacter',array('id_auteur'=>$id_auteur));
	spip_query('DELETE FROM spip_jeux_resultats WHERE  id_resultat NOT IN '.$liste.'and id_auteur='.$id_auteur);	
	}
function jeux_compacter_tout_tout(){
	include_spip('public/assembler');
	$liste = recuperer_fond('fonds/jeux_compacter');
	spip_query('DELETE FROM spip_jeux_resultats WHERE  id_resultat NOT IN '.$liste);
	}
?>