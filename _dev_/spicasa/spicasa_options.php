<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SPICASA',(_DIR_PLUGINS.end($p))."/");


//add LightweightPicasa libary to include_path
ini_set('include_path', 
  ini_get('include_path') . PATH_SEPARATOR . _DIR_PLUGIN_SPICASA.'LightweightPicasaAPI');
require_once 'Picasa.php';

function spicasa_resultados($recherche, $id_article, $debut=null, $max_results=400){

		include_spip('inc/distant'); // pour 'copie_locale'

		$pic = new Picasa();
		$images = $pic->getImages(null, $max_results, null, null, $recherche, "public", null, 1024);

		$compt = 0;
		
		/*$ret = "<table>";*/
		
		foreach($images->getImages() as $img){
		
		/*
			foreach($img->getContentUrlMap() as $key => $value) {
			   $ret .= "<tr><td>".$key."</td><td><a href='".$value."'><img src='".$img->getMediumThumb()."' /></a></td></tr>";
			}
			$ret .= "</table><hr/>";
		*/
	
		
		
			foreach($img->getContentUrlMap() as $key => $value) {
					
				$id = $img->getIdnum();
				$titre = $img->getTitle();
			
			
					$lien = $value;
					
						
			$ret .= "<div style='width: 240px; height: 270px; text-align: center; float: left; margin-right: 10px; margin-bottom: 10px;'>";
			$ret .= "<table cellpadding='0' cellspacing='0'><tr><td style='width: 250px; height: 240px; vertical-align: bottom; text-align: center; border: 0px;'>";
			$ret .= "<a onclick='spicasa_add($id);return false;' href='#'><img src='".$img->getMediumThumb()."' /></a></td></tr></table>";
			$ret .= "<div style='font-size: 0.8em;'><strong>$titre</strong></div>";
			$ret .= "</div>";
			
		}			
		
		
		
	}
	/*
	
	$compt ++;
	}
 
 	if ($compt > 20) {
 		for ($i = 0; $i <= $compt; $i = $i + 20) {
 			if ($i != $debut) $pagination .= " <a href='#' onclick='spicasa_resultados($i);return false;'>$i</a> ";
 			else $pagination .= " <strong>$i</strong> ";
 		}
 		
 		$pagination = "<div style='background-color: #eeeeee; font-size: 0.7em; text-align: right; padding: 5px; padding-right: 10px;'>$pagination</div>";
 		
 	}
 	
 	
 	if ($ret) {
 		$ret = "$pagination<div style='padding: 10px; padding-right: 0px; font-size: 0.8em;'>$ret</div><div style='clear: left;'></div>$pagination";
 
 	}
	*/	
	
 	return $ret;
  
 
    
}



		
		





function spicasa_ajouter(){
	include_spip('inc/distant'); // pour 'copie_locale'


}





?>