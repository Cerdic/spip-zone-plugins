<?php
function inc_smush_image_dist($im){
	
	$image = _image_valeurs_trans($im, "smush");
	
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];
	
	if($creer){
		include_spip('inc/smush_php_compat');
		if(!file_exists($im)){
			spip_log("SMUSH : mauvais chemin pour la fonction smush_image","smush");
			return '';
		}
		spip_log("SMUSH : smush_image pour $im","smush");
		
		// L'adresse de l'API que l'on utilise
		$url_smush = 'http://www.smushit.com/ysmush.it/ws.php';
		
		// On ajoute les paramètres nécessaires pour l'API
		$url_smush_finale = parametre_url($url_smush,'img',url_absolue($im));
		spip_log("SMUSH : recuperation du contenu de $url_smush_finale","smush");
		
		$content = file_get_contents($url_smush_finale);
		$newcontent = json_decode($content, true);
		
		spip_log($newcontent,"smush");
		
		if(!$newcontent['error']){
			include_spip('inc/distant');
			$new_url = 'http://smush.it/'.$newcontent['dest'];
			spip_log("SMUSH : recuperation du fichier $new_url","smush");
			$contenu = recuperer_page($new_url,false,false,_COPIE_LOCALE_MAX_SIZE);
			if (!$contenu) return false;
			ecrire_fichier($im, $contenu);
		}
	}
	
	return _image_ecrire_tag($image,array('src'=>$dest));
}
?>