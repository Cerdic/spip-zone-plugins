<?php
function mao_afficher_contenu_objet($flux){

	$id_mot = $flux['args']['id_objet'];
	$id_groupe = sql_getfetsel('id_groupe','spip_mots','id_mot='.intval($id_mot));
	$page = recuperer_fond("prive/contenu/mot", array('id_mot'=>$id_mot,'id_groupe'=>$id_groupe),true,$connect);
	
	$flux['data'] .= $page;

	return $flux;
}
