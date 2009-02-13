<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SPICASA',(_DIR_PLUGINS.end($p))."/");

ini_set('include_path', 
				ini_get('include_path') . PATH_SEPARATOR . _DIR_PLUGIN_SPICASA.'LightweightPicasaAPI');

require_once 'Picasa.php';

include_spip('inc/distant'); // pour 'copie_locale'


function spicasa_resultados($recherche, $id_article, $debut=1, $max_results=250, $items_page=50){





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
									
				$ret .= "<div style='width: 190px; height: 190px; text-align: center; float: left; margin-right: 10px; margin-bottom: 10px;'>";
				$ret .= "<table cellpadding='0' cellspacing='0'><tr><td style='width: 190px; height: 190px; vertical-align: bottom; text-align: center; border: 0px;'>";
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
	
	
	define('BUFSIZ', 2097152);
	$dir = dirname($_SERVER["SCRIPT_FILENAME"])."/IMG/";


	//$img_local = copie_locale($GLOBALS['meta']['adresse_site']."/IMG/".basename($url));
	$img_local = copie_locale($url);
	
	$taille = filesize($img_local);
	$tam = getimagesize($img_local);
	$img_local = ereg_replace("^"._DIR_IMG, "", $img_local);

	
	include_spip("base/abstract_sql");
	$id_document = sql_insertq (
		"spip_documents",
		array (
			"extension" => "$extension",
			"titre" => "$titre",
			"date" => "NOW()",
			"descriptif" => "$descriptif",
			"fichier" => "$img_local",
			"largeur" => "$tam[0]",
			"hauteur" => "$tam[1]",
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
	
	return "La imágen $titre se cargó exitosamente";

}

function spicasa_login($email, $pass){

  
    $pic = new Picasa();

    try{
        $login = $pic->authorizeWithClientLogin($email, $pass);
        print "<script>$('#login').html('<p>Logged as <strong>$email</strong></p>');</script>";
        
    } catch (Picasa_Exception_CaptchaRequiredException $ce) {
        print "Por favor, ingrese el captcha ";
        print '<img src="'.$ce->getCaptchaUrl().'" />';

       /* Put code for generating a form with an input field, setting $ce->getCaptchaToken(),
        * $ce->getUsername(), and $cd->getPassword() as hidden fields here
        */
    } catch (Picasa_Exception_InvalidUsernameOrPasswordException $ie) {
        print "The username or password you have entered is invalid.";

        /* Put code for handling re-logins here
        */
    } catch (Picasa_Exception $e) {
        print "Your attempt to login has failed: ".$e->getMessage();

        /* Put code for handling relogins here
        */
    }
    $username = substr($email, 0, strrpos($email, "@")); 
    //print $username;
    $account = $pic->getAlbumsByUsername ($username);
    
     foreach($account->getAlbums() as $album){
        $ret .=  spicasa_thumb_album($album->getIdnum(),$album->getIcon(),$album->getTitle(), $username);
     }
    
    return $ret;
}


function spicasa_add_album($id_album, $user, $id_article){

	 $pic = new Picasa();


     /*
     getAlbumById  (string $username, string $albumid, [int $maxResults = null], [int $startIndex = null], [string $keywords = null], [string $tags = null], [int $thumbsize = null], [int $imgmax = null], string $visibility) 
     */

     $album = $pic->getAlbumById($user, $id_album, null, null, null, null, null, 800);
     foreach($album->getImages() as $img){    
            	$id_image = $img->getIdnum();
    	        spicasa_add($id_image, $id_article, $id_album, $user);
     }
     
}



function spicasa_thumb_album($id_album, $icon, $titre, $author) {

			$ret .= "<div style='width: 240px; height: 270px; text-align: center; float: left; margin-right: 10px; margin-bottom: 10px;'>";
				$ret .= "<table cellpadding='0' cellspacing='0'><tr><td style='width: 250px; height: 240px; vertical-align: bottom; text-align: center; border: 0px;'>";
				$ret .= "<a onclick='spicasa_add_album(\"$id_album\",\"$author\");return false;' href='#'><img src='".$icon."' /></a></td></tr></table>";
				$ret .= "<div style='font-size: 0.8em;'><strong>$titre</strong></div>";
				$ret .= "</div>";
    return $ret;
			
}

?>
