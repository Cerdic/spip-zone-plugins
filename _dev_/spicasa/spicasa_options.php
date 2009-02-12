<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SPICASA',(_DIR_PLUGINS.end($p))."/");



function spicasa_resultados($recherche, $id_article, $debut=1, $max_results=40, $items_page=40){
		//add LightweightPicasa libary to include_path
		ini_set('include_path', 
				ini_get('include_path') . PATH_SEPARATOR . _DIR_PLUGIN_SPICASA.'LightweightPicasaAPI');
		
		require_once 'Picasa.php';



		include_spip('inc/distant'); // pour 'copie_locale'

		$pic = new Picasa();
		$images = $pic->getImages(null, $debut+$items_page, $debut, $recherche, null, "public", null, 800);

		if($images->getTotalResults() < $max_results) $max_results=$images->getTotalResults();
		
		foreach($images->getImages() as $img){
			
					
				$id_image = $img->getIdnum();
				$id_album = $img->getAlbumid(); //$img->getAlbumid();
				//print "<script>console.log($id_album);</script>";
				$author = $img->getAuthor()->getUser();
				
				$titre = $img->getTitle();
					
					
					
				//$url_image = $value;
									
				$ret .= "<div style='width: 240px; height: 270px; text-align: center; float: left; margin-right: 10px; margin-bottom: 10px;'>";
				$ret .= "<table cellpadding='0' cellspacing='0'><tr><td style='width: 250px; height: 240px; vertical-align: bottom; text-align: center; border: 0px;'>";
				$ret .= "<a onclick='spicasa_add(\"$id_image\",\"$id_album\",\"$author\");return false;' href='#'><img src='".$img->getMediumThumb()."' /></a></td></tr></table>";
				$ret .= "<div style='font-size: 0.8em;'><strong>$titre</strong></div>";
				$ret .= "</div>";
			
		}
	
 	//pagination
 	for ($i = 1; $i <= $max_results; $i = $i + $items_page) {
 		if ($i != $debut) $pagination .= " <a href='#' onclick='spicasa_buscar($i);return false;'>$i</a> ";
 		else $pagination .= " <strong>$i</strong> ";
 	}
 		
 		$pagination = "<div style='background-color: #eeeeee; font-size: 0.7em; text-align: right; padding: 5px; padding-right: 10px;'>$pagination</div>";
 	
 	
 	if ($ret) {
 		$ret = "$pagination<div style='padding: 10px; padding-right: 0px; font-size: 0.8em;'>$ret</div><div style='clear: left;'></div>$pagination";
 	}
	
	
 	return $ret;
  
 
    
}



		
		

function spicasa_add($id_image, $id_article, $id_album, $user){
	include_spip('inc/distant'); // for 'copie_locale'
	
	ini_set('include_path', 
				ini_get('include_path') . PATH_SEPARATOR . _DIR_PLUGIN_SPICASA.'LightweightPicasaAPI');
		
	
	require_once 'Picasa.php';

	//print "<script>console.log(\"$id_album\");</script>";
	
	/*
	print $id_image."<br>";
	print $id_article."<br>";
	print $id_album."<br>";
	print $user."<br>";
	*/
	
	
	
	
	
	$pic = new Picasa();
	$image = $pic->getImageById($user, $id_album, $id_image, null, 800);
	foreach($image->getContentUrlMap() as $value) $url = $value; //just one

	$type = $image->getImageType();
		
	switch($type){
		case "image/jpeg": 
			$extension = "jpg";	
			break;
		case "image/gif": 
					$extension = "gif";	
					break;
		case "image/png":
			$extension = "png";	
			break;		
	}

	$titre = $image->getTitle();
	$descriptif = $image->getDescription()."(c)".$image->getAuthor()->getName() ;
	$largeur = $image->getWidth();
	$hauteur = $image->getHeight();
	
	
    
   

	//$url = $GLOBALS['meta']['adresse_site']."/?page=picasaimage&url=".$url;
	print "<a href='$url'>remote</a><br>";
	
	
	define('BUFSIZ', 4095);
	$dir = dirname($_SERVER["SCRIPT_FILENAME"])."/IMG/";


	//$img_local = copie_locale($GLOBALS['meta']['adresse_site']."/IMG/".basename($url));
	$img_local = copie_locale($url);
	
	echo "local: ".$img_local;
	
	
	//$img_local = ereg_replace("^"._DIR_IMG, "", $img_local);

	
	$taille = filesize($img_local);
	
	
	include_spip("base/abstract_sql");
	$id_document = sql_insertq (
		"spip_documents",
		array (
			"extension" => "$extension",
			"titre" => "$titre",
			"date" => "NOW()",
			"descriptif" => "$descriptif",
			"fichier" => "$img_local",
			"largeur" => "$largeur",
			"hauteur" => "$hauteur",
			"mode" => "document",
			"taille" => $taille,
			"distant" => "non"
		)
	);
	if ($id_document) {
		sql_insertq (
			"spip_documents_liens",
			array(
				"id_document" => $id_document,
				"id_objet" => $id_article,
				"objet" => "article"
			)
		);
	}
	
	print "ok";
	


}








?>
