<?php
$GLOBALS['spip_matrice']['image_gif_split'] = 'animated_GIF_fonctions.php';
//$GLOBALS['spip_matrice']['image_gif_animData'] = 'animated_GIF_fonctions.php';
//non faccio passare image_gif_join per filtrer
//$GLOBALS['spip_matrice']['image_gif_join'] = 'animated_GIF_fonctions.php';

function is_animated_gif( $filename, &$raw )
{
  $raw = file_get_contents( $filename );

  $offset = 0;
  $frames = 0;
  while ($frames < 2) {
    $where1 = strpos($raw, "\x00\x21\xF9\x04", $offset);
    if ( $where1 === false ) {
      break;
    } else {
      $offset = $where1 + 1;
      $where2 = strpos( $raw, "\x00\x2C", $offset );
      if ( $where2 === false ) {
        break;
      } else {
        if ( $where1 + 8 == $where2 ) {
          $frames ++;
        }
        $offset = $where2 + 1;
      }
    }
  }

  return $frames > 1;
}

function image_gif_animData($img) {
  $fonction = array('image_gif_split',func_get_args());
	include_spip("inc/filtres_images_lib_mini");
  $img = _image_valeurs_trans($img, "gif_split", false, $fonction);
  if (!$img) return "";

  $gifdata = "";
  if($img["format_source"]!="gif" || !is_animated_gif($img["fichier"],$gifdata))
    return "";

  include_once("GIFDecoder.class.php");
  $gifDecoder = new GIFDecoder($gifdata);
  return array(
    "delays" => $gifDecoder->GIFGetDelays(),
    "loop" => $gifDecoder->GIFGetLoop(),
    "disposal" => $gifDecoder->GIFGetDisposal(),
    "transparentR" => $gifDecoder->GIFGetTransparentR(),
    "transparentG" => $gifDecoder->GIFGetTransparentG(),
    "transparentB" => $gifDecoder->GIFGetTransparentB()
  );
}

function image_gif_split($img) {

  $fonction = array('image_gif_split',func_get_args());
  $img = _image_valeurs_trans($img, "gif_split", false, $fonction);
  //var_dump($img); 
	if (!$img) return "";
  
  //verifico gif animata
  $gifdata = "";
  if($img["format_source"]!="gif" || !is_animated_gif($img["fichier"],$gifdata))
    return $img;

  //se si splitta sorgente in vari frames ed elaborali
  include_once("GIFDecoder.class.php");
  $gifDecoder = new GIFDecoder($gifdata);

	$i = 1;
	$gifArray = $gifDecoder->GIFGetFrames();
	$fichier_dest = $img["fichier_dest"];
  $tag = $img;
  $img["tag"] = "";
  
  foreach ($gifArray as $frame) {
    if ($i<10) {
			$fichierframe = preg_replace("/(.*)\.png$/",'$1'."frame0$i.gif",$fichier_dest);
		}
		else {
			$fichierframe = preg_replace("/(.*)\.png$/",'$1'."frame$i.gif",$fichier_dest);
		}
		if(!@file_exists($fichierframe))
      fwrite(fopen($fichierframe, "wb"), $frame);
		if (@file_exists($fichierframe)){
			// dans tous les cas mettre a jour la taille de l'image finale
			list ($valeurs["hauteur_dest"],$valeurs["largeur_dest"]) = taille_image($valeurs['fichier_dest']);
			$img['date'] = @filemtime($fichierframe); // pour la retrouver apres disparition
			ecrire_fichier($fichierframe.'.src',serialize($img),true);
		}
    
    $i++;
    $img["tag"] .= _image_ecrire_tag($tag,array("src" => $fichierframe)); 
  }
  
  return $img["tag"];
}

function image_gif_join($img,$animData) {
  $fonction = array('image_gif_join',func_get_args());
	//include_spip("inc/filtres_images_lib_mini");
  $img = _image_valeurs_trans($img, "gif_join", "gif", $fonction);
  if (!$img) return("");
  
  $destArray = extraire_balises($img["tag"],"img");
  
  for($i=0,$l=count($destArray);$i<$l;$i++) 
    $destArray[$i] = extraire_attribut(filtrer("image_format",$destArray[$i],"gif"),"src");
  
  include_once("GIFEncoder.class.php");
  $animatedgif = new GIFEncoder(
    $destArray,
    $animData["delays"],
    $animData["loop"],
    $animData["disposal"],
    $animData["transparentR"],
    $animData["transparentG"],
    $animData["transparentB"],
    "url"
  );
  
  ecrire_fichier($img["fichier_dest"],$animatedgif->GetAnimation(),true);
  $img["tag"] = extraire_balise($img["tag"],"img");
  
  return _image_ecrire_tag($img,array("src" => $img["fichier_dest"]));
}

