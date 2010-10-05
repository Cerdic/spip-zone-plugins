<?php

function animatedGIF_image_preparer_filtre($ret) {
  $args = $ret["args"];
  if($args["effet"]=="gif_split")
    return $ret;
  
  $data = $ret["data"];
  
  //verifico gif animata
  $gifdata = "";
  if($data["format_source"]!="gif" || !is_animated_gif($data["fichier"],$gifdata))
    return $ret;
  
  $animData = image_gif_animData($args["img"]);
  
  $args["fonction_creation"][1][0] = filtrer("image_gif_split",$args["fonction_creation"][1][0]);
  
  $params = array_merge(array($args["fonction_creation"][0]),$args["fonction_creation"][1]);
  
  $data["fichier_dest"] = extraire_attribut(image_gif_join(call_user_func_array("filtrer",$params),$animData),"src");
  $data["tag"] = _image_ecrire_tag($data,array("src" => $data["fichier_dest"]));
  
  $data["creer"] = false;
  $ret["args"] = $args;
  $ret["data"] = $data;
  return $ret;
}
