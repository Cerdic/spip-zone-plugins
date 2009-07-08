<?php
// PLUGIN G2EMBED : Gallery2/Spip integration
// Author : Philippe GRISON - UPS 2259 - CNRS
// last modified : 8 July 2009
// Licence: GPL
// ---------------------------------------------------------------------------------------- //
// DECLARATION DES FONCTIONS 
// ----------------------------------------------------------------------------------------

// G2_PHOTOS($id, $size_img) : AFFICHAGE D'UNE PHOTO DE GALLERY ET DE SES INFOS RELATIVES
// $size_img : taille Maxi de l'image a afficher
// $id : l'identifaiant dans Gallery 
function  g2_photos($id, $size_img) {
	// Includes
	include(dirname(__FILE__) . '/g2embed_options.php'); // g2 path : $my_g2Uri, $my_embedUri				
	require_once(dirname(__FILE__) . $my_g2embed ); 	//  /gallery2/embed.php
	
	// Precise le type de HEADER a envoyer
	if (!headers_sent()) { header('Content-Type: text/html; charset=UTF-8');}
	
	
	// initiate G2 en FULLINIT
	$ret = GalleryEmbed::init(array('fullInit' => True,
									'embedUri' =>  $my_embedUri,
									'g2Uri' => $my_g2Uri));
	if ($ret) { // message d'erreur si l'initialisation a echoue
	         print "GalleryEmbed::init failed, here is the error message: <br>";
             print $ret->getAsHtml();
			 return "GalleryEmbed::init failed, here is the error message: <br>";
             exit;
	}
	 
	// test sur les permissions 
	list ($status, $permission) = GalleryCoreApi::getPermissions($id);		
	$flux_html='';
	// l'acces aux versions "Resized" doivent etre permis
	if (array_key_exists('core.viewResizes', $permission))  {	
		// Appel du Block via la fonction getBlock()
		$params = array('blocks' => 'specificItem',
						'showTitle' => 1,
						'showOwner' => 1,
						'showDate' => 1,
						'itemId' => $id,
						'maxSize' => $size_img,
						'useDefaults' => 0 ,
						'link' => 'none');		
		// ATTENTION :  getBlock n'existe qu'a partir de la version 1.1.9 du plugin "image block"
		// la version 1.1.9 du plugin "image block" demande une version de gallery2 >= 2.3
		$class_methods = get_class_methods('GalleryEmbed');
		if (array_search('getBlock', $class_methods)) {
		list($status,$html,$head1)=GalleryEmbed::getBlock('imageblock','ImageBlock',$params);			
			if ($status) { // affichage du message d'erreur si  getBlock a echoue
				return "<blink><font color=red>Error 2 inserting itemId
				$id</font></blink>" .
				"<hr>Gallery 2 error code: " . $status->getErrorCode() .
				"<br>Gallery 2 error text: " . $status->getErrorMessage() .
				"<br> Error details: <br> " . $status->getAsHtml();
				exit;
			  }
			 $flux_html = $html;
		} else {
				return "<blink><font color=red>Error ! Unknown function getBlock</font></blink>".
				"<hr>check the version of  \"image block\" plugin. It needs a version >= 1.1.9";
				exit;
		}
		
	} 
   return $flux_html;
}



// Balise

function balise_GPHOTO($p) 
{
  if ($p->param && !$p->param[0][0]) { 
    $id=calculer_liste($p->param[0][1],$p->descr,$p->boucles,$p->id_boucle);
    $params->code = "g2_photos((int) $id, 525)";
    $params->type = 'html';
    return $params;
  } else {
    $params->code = '<span style="color:red">Error!</span>';
    $params->type = 'html';
    return $params;
  }
}




?>
