<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function urls_par_numero_propres_creer_chaine_url($data){
	if ($data['objet']['type'] == 'article'){
		$id_article = $data['objet']['id_objet']; 
		$data['data']=$id_article;
		$data['objet']['url']=$id_article;
	}
	return $data;
}
