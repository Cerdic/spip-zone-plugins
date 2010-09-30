<?php

function formulaires_dotclear2spip_charger(){
	$blog = _request('blog');
	if ($blog == null)
		$blog = array();
	return array('blog'=>$blog);
}

function formulaires_dotclear2spip_verifier(){
	$confirm = _request('confirm');
	$blog    = _request('blog');
	if ($confirm and count($blog)>0){
		return array();
	}
	
	else if (count($blog)>0){
		$erreur["message_erreur"] = "Confirmez vous la migration des blogs suivants : ". implode($blog,' - ') . " ? ";
		return $erreur;
	}
	else{
		$erreur["message_erreur"] = "Veuillez cocher au moins un blog";
		return $erreur;
	}
}

function formulaires_dotclear2spip_traiter(){
	include_spip('dot2_fonctions');
	$blogs    = _request('blog');
	foreach ($blogs as $blog){
		dot_migrer_blog($blog);
			
	}
}


?>