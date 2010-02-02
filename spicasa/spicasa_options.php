<?php
function spicasa_init(){
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SPICASA',(_DIR_PLUGINS.end($p))."/");

ini_set('include_path', 
				ini_get('include_path') . PATH_SEPARATOR . _DIR_PLUGIN_SPICASA.'LightweightPicasaAPI');

require_once 'Picasa.php';

include_spip('inc/distant'); // pour 'copie_locale'
}


function spicasa_resultados($query, $id_article, $debut=1, $max_results=250, $items_page=50){
        /*Return images for a general query*/
        spicasa_init();    

		$pic = new Picasa();
		$query = str_replace(" ", "+", $query);
		$images = $pic->getImages(null, $debut+$items_page, $debut, $query, null, "public", null, 800);

		if($images->getTotalResults() < $max_results) $max_results=$images->getTotalResults();
		
		foreach($images->getImages() as $img){
			
					
				$id_image = $img->getIdnum();
				$id_album = $img->getAlbumid(); 
				$author = $img->getAuthor()->getUser();
				
				$titre = $img->getTitle();
				$thumb = $img->getMediumThumb();
					
		        $ret .= spicasa_thumb($id_image,$id_album, $thumb, $titre, $author,"");
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


function spicasa_add_photo($id_image, $id_article, $id_album, $user){
    /* This function download the given image and attach it to the articule in course.*/
    spicasa_init();    
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
   
   
  
	echo "<script>alert('url: ".$url."');</script><br><br>";


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
	
	return _T('spicasa:la_imagen')." <strong>".$titre."</strong> "._T('spicasa:exitosamente');

}

function spicasa_login($email, $pass){
    spicasa_init();
    /* function to login at Picasa Web album. If it's ok, show the list of user's albums */
    $pic = new Picasa();

    try{
        $login = $pic->authorizeWithClientLogin($email, $pass);
        print "<script>$('#login').html('<p>"._T('spicasa:logged')."<strong>$email</strong></p>');</script>";
        
    } catch (Picasa_Exception_CaptchaRequiredException $ce) {
        print _T('spicasa:captcha');
        print '<img src="'.$ce->getCaptchaUrl().'" />';
        return;

       /* Put code for generating a form with an input field, setting $ce->getCaptchaToken(),
        * $ce->getUsername(), and $cd->getPassword() as hidden fields here
        */
    } catch (Picasa_Exception_InvalidUsernameOrPasswordException $ie) {
        print _T('spicasa:login_invalid');
        print _T('spicasa:intente');
        return;
    } catch (Picasa_Exception $e) {
        print _T('spicasa:login_fail').": ".$e->getMessage();
        print "<br/>"._T('spicasa:intente');
        return;

    }
   
    //show album list. 
    return spicasa_lists_albums($email, $pic);
}

function spicasa_lists_albums($email, $pic){
    $username = substr($email, 0, strrpos($email, "@")); 
    //print $username;
    $account = $pic->getAlbumsByUsername ($username, null, null, "all");
    
     foreach($account->getAlbums() as $album){
        $ret .=  spicasa_thumb("",$album->getIdnum(),$album->getIcon(),$album->getTitle(), $username, "album");
     }
    
    return $ret;
  }



function spicasa_add_album($id_album, $user, $id_article){
         spicasa_init();
    	 $pic = new Picasa();
	     $album = $pic->getAlbumById($user, $id_album, null, null, null, null, null, 800);
         foreach($album->getImages() as $img){    
            	$id_image = $img->getIdnum();
    	        spicasa_add_photo($id_image, $id_article, $id_album, $user);
        }

        return _T('spicasa:el_album')." <strong>".$album->getTitle()."</strong> "._T('spicasa:exitosamente');
        
}


function spicasa_show_album($id_album, $user, $id_article){
     spicasa_init();
	 $pic = new Picasa();
     $album = $pic->getAlbumById($user, $id_album, null, null, null, null, null, 800);
     foreach($album->getImages() as $img){    
            	$id_image = $img->getIdnum();
				$id_album = $img->getAlbumid(); 
				$author = $img->getAuthor()->getUser();
				
				$titre = $img->getTitle();
				$thumb = $img->getMediumThumb();
					
		        $ret .= spicasa_thumb($id_image,$id_album, $thumb, $titre, $author,"");
     }
     return $ret;
    
}




function spicasa_thumb($id_image,$id_album, $thumb, $titre, $author, $type="photo") {

    			$ret .= "<div style='width: 190px; height: 190px; text-align: center; float: left; margin-right: 10px; margin-bottom: 10px;'>";
				$ret .= "<table cellpadding='0' cellspacing='0'><tr><td style='width: 190px; height: 190px; vertical-align: bottom; text-align: center; border: 0px;'>";
				$ret .= "<a onclick='spicasa";
            	$ret .= ($type=="album") ? "_show_album": "_add_photo";

				$ret .= "(";
				if ($id_image) $ret .= "\"$id_image\", ";
				$ret .= "\"$id_album\",\"$author\");return false;' href='#'><img src='".$thumb."' /></a></td></tr></table>";
				$ret .= "<div style='font-size: 0.8em;'><strong>$titre</strong></div>";
				$ret .= "</div>";
                return $ret;
}


?>
