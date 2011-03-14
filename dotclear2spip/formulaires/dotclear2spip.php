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
	
	return array('message_ok'=>
		"La migration s'est bien effectuée. Il vous reste :
		<ul>
			<li>A regarder le fichier tmp/prive_dot_attention.log pour vérifier s'il n'y pas encore des réglages à faire à la main (notamment pour les documents).</li>
			<li>A recopier le contenu du fichier local/htaccess.txt dans votre fichier, en ayant remplacé au préalable http://exemple.tld par l'adresse du site</li>
			<li><a href='".generer_url_ecrire('admin_plugin')."'>A désactiver ce plugin</a>, voire à le supprimer du répertoire FTP</li>
		</ul>"
	
	);
}


?>