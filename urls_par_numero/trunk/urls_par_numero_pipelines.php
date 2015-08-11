<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function urls_par_numero_propres_creer_chaine_url($data){
	if ($data['objet']['type'] == 'article'){
		$id_article = $data['objet']['id_objet'];
		$data['data']=$id_article;
		$data['objet']['url']=$id_article;
		settype($data['data'],'string');
		settype($data['objet']['url'],'string');
		// supprimer l'arborescence
		unset($data['objet']['parent']);
		unset($data['objet']['id_parent']);
		unset($data['objet']['type_parent']);
	}

	return $data;
}

function urls_par_numero_arbo_creer_chaine_url($data){
	$data = urls_par_numero_propres_creer_chaine_url($data);
	return $data;
}
