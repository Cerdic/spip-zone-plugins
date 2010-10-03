<?php

function animatedGIF__image_valeurs_trans($ret) {
  $data = $ret["data"];
  
  //verifico gif animata
  $gifdata = "";
  if($data["format_source"]!="gif" || !is_animated_gif($data["fichier"],$gifdata))
    return $ret;

  //se si splitta sorgente in vari frames ed elaborali
  include_once("GIFDecoder.class.php");
  $gifDecoder = new GIFDecoder($gifdata);

	$i = 1;
	$gifArray = $gifDecoder->GIFGetFrames();
	$fichier_orig = $data["fichier"];

	$destArray = array();
  foreach ($gifArray as $frame) {
    if ($i<10) {
			$fichierframe = $fichier_orig."frame0$i.gif";
		}
		else {
			$fichierframe = $fichier_orig."frame$i.gif";
		}
		if(!@file_exists($fichierframe))
      fwrite(fopen($fichierframe, "wb"), $frame);
    $i++;
    $img = preg_replace("/(src=[\"'])([^\"']*)([\"'])/",'$1'.$fichierframe.'$3',$data["tag"]);
    
    $args = $data["reconstruction"];
    $image_args = $args[1];
    array_shift($image_args);
    $args[1] = $img;
    $args = array_merge($args,$image_args);
    
    $destArray[] = extraire_attribut(image_graver(filtrer("image_format",call_user_func_array("filtrer",$args),"gif")),"src");
  }
  
  //riunisci gif
  include_once("GIFEncoder.class.php");
  $animatedgif = new GIFEncoder(
    $destArray,
    $gifDecoder->GIFGetDelays(),
    $gifDecoder->GIFGetLoop(),
    $gifDecoder->GIFGetDisposal(),
    $gifDecoder->GIFGetTransparentR(),
    $gifDecoder->GIFGetTransparentG(),
    $gifDecoder->GIFGetTransparentB(),
    "url"
  );
  //cancello immagini intermedie 
  foreach($destArray as $frame) {
    @spip_unlink($frame);
  }

  //salvo immagine finale
  $data["fichier_dest"] = preg_replace("/".$data["format_dest"]."$/","gif",$data["fichier_dest"]);
  $data["tag"] = preg_replace("/".$data["format_dest"]."$/","gif",$data["tag"]);
  ecrire_fichier($data["fichier_dest"],$animatedgif->GetAnimation(),true);  
  
  $data["creer"] = false;
  $ret["data"] = $data;
  return $ret;
}

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
