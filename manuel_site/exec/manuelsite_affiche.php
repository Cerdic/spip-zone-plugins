<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");

function exec_manuelsite_affiche(){

	$id_article = intval(_request('id_article'));
	//
	// Affichage de la page
	//
	if($id_article){
		echo recuperer_fond('prive/inclure/manuelsite_article',array('id_article'=>$id_article));
	}else{
		echo _T('manuelsite:erreur_pas_darticle') ;
	}
}
?>