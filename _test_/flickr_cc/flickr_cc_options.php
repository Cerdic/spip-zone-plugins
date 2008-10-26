<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_FLICKR_CC',(_DIR_PLUGINS.end($p))."/");


define ("_KEY_API_FLICKR", "5cbac69cf7ddbde0ce09baf867f56ccd");


function flickr_ajouter ($id_image, $id_article) {
	include_spip('inc/distant'); // pour 'copie_locale'

	$url = "http://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key="._KEY_API_FLICKR."&photo_id=".$id_image;
	
	echo "<div>$url</div>";
	$contenu = recuperer_page($url,false,false,_COPIE_LOCALE_MAX_SIZE);
	
	if (ereg("label=\"Medium\" width=\"([0-9]+)\" height=\"([0-9]+)\" source=\"([^\"]*)\"", $contenu, $regs)) {
		$largeur = $regs[1];
		$hauteur = $regs[2];
		$image = $regs[3];
	}
			
	if (ereg("label=\"Large\" width=\"([0-9]+)\" height=\"([0-9]+)\" source=\"([^\"]*)\"", $contenu, $regs)) {
		$largeur = $regs[1];
		$hauteur = $regs[2];
		$image = $regs[3];
	}
		
	/* En largeur 1024, ca devrait suffir pour des images...
	if (ereg("label=\"Original\" width=\"([0-9]+)\" height=\"([0-9]+)\" source=\"([^\"]*)\"", $contenu, $regs)) {
		$largeur = $regs[1];
		$hauteur = $regs[2];
		$image = $regs[3];
	}
	*/
		
	echo $image;
	echo " - $largeur x $hauteur";
	
	$extension = substr($image, strlen($image)-3, 3);
	
	echo " ($extension)";

	$url = "http://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key="._KEY_API_FLICKR."&photo_id=".$id_image;
	$contenu = recuperer_page($url,false,false,_COPIE_LOCALE_MAX_SIZE);
//	$contenu = str_replace("<", "&lt;", $contenu);
	
//	echo $contenu;
	
	if (ereg("<title>(.*)</title>", $contenu, $regs2)) {
		$titre = $regs2[1];
		
		echo "<div>$titre</div>";
	}
	
	
	if (ereg("username=\"([^\"]*)\" realname=\"([^\"]*)\"", $contenu, $regs2)) {
		$realname = $regs2[2];
		$username = $regs2[1];
		
		if (strlen($realname) > 0) $username = $realname;
		
		echo "<div>$username</div>";
	}
	if (ereg("<url type=\"photopage\">(.*)</url>", $contenu, $regs2)) {
		$page = $regs2[1];
		
		echo "<div>$page</div>";
		
		$username = "(cc) <a href='$page'>$username</a>";
	}

	$image = copie_locale($image);
	
	$taille = filesize($image);
	echo "<div>taille: $taille</div>";
	
	$image = ereg_replace("^"._DIR_IMG, "", $image);
	
	echo $image;

	include_spip("base/abstract_sql");
	$id_document = sql_insertq (
		"spip_documents",
		array (
			"extension" => "$extension",
			"titre" => "$titre",
			"date" => "NOW()",
			"descriptif" => "$username",
			"fichier" => "$image",
			"largeur" => "$largeur",
			"hauteur" => "$hauteur",
			"mode" => "image",
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


}

function flick_cc_resultat ($recherche, $id_article, $debut=0, $champs, $ordre="relevance") {
	$recherche = rawurlencode($recherche);
	
	$champs = substr($champs, 1, 1000);

	$url = "http://api.flickr.com/services/rest/?method=flickr.photos.search&text=$recherche&api_key="._KEY_API_FLICKR."&privacy_filter=1&license=$champs&sort=$ordre&media=photos&extras=owner_name&per_page=500";
	
	$fichier_flickr = sous_repertoire(_DIR_VAR, 'cache-flickr') . md5($url).".xml";	
	$date_init = time() -  60 * 60 * 24;

	// Systeme de cache pour les variables exif								
	if (file_exists($fichier_flickr) && @filemtime($fichier_flickr) > $date_init) {
		lire_fichier($fichier_flickr, $contenu);
	} else {
		include_spip('inc/distant'); // pour 'copie_locale'
		include_spip('inc/documents'); // pour 'set_spip_doc'
		
		$contenu = recuperer_page($url,false,false,_COPIE_LOCALE_MAX_SIZE);
		
		ecrire_fichier($fichier_flickr, $contenu);
	}
	//$contenu = str_replace("<", "&lt;", $contenu);
	
	preg_match_all("/<photo id.* \/>/", $contenu, $out);
	
	$out = $out[0];

	$compt = 0;
	foreach($out as $k=>$value) {
		
		if ($compt >= $debut && $compt < $debut + 20) {
			if(ereg("id=\"([^\"]*)\".*owner=\"([^\"]*)\".*secret=\"([^\"]*)\".*server=\"([^\"]*)\".*farm=\"([^\"]*)\".*title=\"([^\"]*)\".*ownername=\"([^\"]*)\"", $value, $regs)) {
				$id = $regs[1];
				$user_id = $regs[2];
				$secret = $regs[3];
				$server_id = $regs[4];
				$farm_id = $regs[5];
				$titre = couper(typo($regs[6]), 70);
				$nom = typo($regs[7]);
				
				$lien = "http://www.flickr.com/photos/$user_id/$id";
				
				$ret .= "<div style='width: 240px; height: 270px; text-align: center; float: left; margin-right: 10px; margin-bottom: 10px;'>";
				$ret .= "<table cellpadding='0' cellspacing='0'><tr><td style='width: 250px; height: 240px; vertical-align: bottom; text-align: center; border: 0px;'><a onclick='ajouter_flickr($id);return false;' href='#'><img src='http://farm".$farm_id.".static.flickr.com/".$server_id."/".$id."_".$secret."_m.jpg' /></a></td></tr></table>";
				$ret .= "<div style='font-size: 0.8em;'><strong>$titre</strong><br /><a href='$lien'>$nom</a></div>";
				$ret .= "</div>";
				
								
			
			}
		}

			$compt ++;

//	<photo id="55582632" owner="60843921@N00" secret="0ee7885f06" server="32" farm="1" title="Montmartre" ispublic="1" isfriend="0" isfamily="0" ownername="John Althouse Cohen" />
//	http://www.flickr.com/photos/{user-id}/{photo-id}		
		
		
	}
	
	if ($compt > 20) {
		for ($i = 0; $i <= $compt; $i = $i + 20) {
			if ($i != $debut) $pagination .= " <a href='#' onclick='resultat_flickr($i);return false;'>$i</a> ";
			else $pagination .= " <strong>$i</strong> ";
		}
		
		$pagination = "<div style='background-color: #eeeeee; font-size: 0.7em; text-align: right; padding: 5px; padding-right: 10px;'>$pagination</div>";
		
	}
	
	
	if ($ret) {
		$ret = "$pagination<div style='padding: 10px; padding-right: 0px; font-size: 0.8em;'>$ret</div><div style='clear: left;'></div>$pagination";

	}
	
	return $ret;


}


?>