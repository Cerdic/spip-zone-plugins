<?php
/*
* BOUCLEs Flickr API
* 
* Auteur: Mortimer (Pierre Andrews)
* (c) 2006 - Distribue sous license GNU/GPL
*/

include_spip('base/FpipR_temporaire');

function critere_tags($idb, &$boucles, $crit) {
}

function critere_tag_mode($idb, &$boucles, $crit) {
}

function critere_text($idb, &$boucles, $crit) {
}

function critere_privacy_filter($idb, &$boucles, $crit) {
}

function critere_bbox($idb, &$boucles, $crit) {
}


function critere_accuracy($idb, &$boucles, $crit) {
}


/** boucle FLICKR_PHOTOS_SEARCH
Voir la doc de l'API: http://flickr.com/services/api/flickr.photos.search.html
user_id V
tags V
tag_mode V
text V
upload_date
taken_date
license: V
    <license id="4" name="Attribution License"
		url="http://creativecommons.org/licenses/by/2.0/" /> 
	<license id="6" name="Attribution-NoDerivs License"
		url="http://creativecommons.org/licenses/by-nd/2.0/" /> 
	<license id="3" name="Attribution-NonCommercial-NoDerivs License"
		url="http://creativecommons.org/licenses/by-nc-nd/2.0/" /> 
	<license id="2" name="Attribution-NonCommercial License"
		url="http://creativecommons.org/licenses/by-nc/2.0/" /> 
	<license id="1" name="Attribution-NonCommercial-ShareAlike License"
		url="http://creativecommons.org/licenses/by-nc-sa/2.0/" /> 
	<license id="5" name="Attribution-ShareAlike License"
		url="http://creativecommons.org/licenses/by-sa/2.0/" /> 
privacy_filter X
    * 1 public photos
    * 2 private photos visible to friends
    * 3 private photos visible to family
    * 4 private photos visible to friends & family
    * 5 completely private photos
bbox min_lon:min_lat:max_lon:max_lat V
accuracy V
    * World level is 1
    * Country is ~3
    * Region is ~6
    * City is ~11
    * Street is ~16
*/
function boucle_FLICKR_PHOTOS_SEARCH_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_fpipr_photos";

	$possible_args = array('user_id','license','upload_date','taken_date');

	$possible_criteres = array('tags','tag_mode','text','privacy_filter',
					  'bbox','accuracy');

	$arguments = '';


	//on regarde dans le contexte si les arguments possible sont dispo.
/*	Foreach($possible_args as $key) {
		$champ = new Champ;
		$champ->nom_champ = $key;
		$arguments[$key] = calculer_liste(array($champ),array(), $boucles, $boucle->$id_boucle);
	}*/

	//on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
	foreach($boucle->criteres as $crit) {
	  if (in_array($crit->op,$possible_criteres)){
		$val = !isset($crit->param[0]) ? "" : calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
		$arguments[$crit->op] = $val;
	  }
	}
	foreach($boucle->where as $w) {
	  $key = str_replace("'",'',$w[1]);
	  $key = str_replace("$id_table.",'',$key);
	  $val = $w[2];
	  if (in_array($key,$possible_args)){
		//TODO upload_date doit être en timestamp/1000
		  switch($w[0]) {
			case "'='":
			  if($key == 'taken_date' || $key == 'upload_date') {
				$arguments['min_'.$key] = $val;
				$arguments['max_'.$key] = $val;
			  } else {
				$arguments[$key] = $val;
			  }
			  break;
			case "'<'":
			  if($key == 'taken_date' || $key == 'upload_date') {
				$arguments['min_'.$key] = $val;
			  }
			  break;
			case "'>'":
			  if($key == 'taken_date' || $key == 'upload_date') {
				$arguments['max_'.$key] = $val;
			  }
			  break;
	 	  }
	  }
	  }
	$boucle->hash = "// CREER la table temporaire flickr_photos et la peupler avec le resultat de la query
\$arguments = '';\n";
	$bbox = '';
	foreach($arguments as $key => $val) {
	  if($val) {
	  	$boucle->hash .= "\$v=$val;\n";
	  	$boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	  }}

	$boucle->hash .= "FpipR_fill_table_temporaire_boucle('flickr.photos.search',\$arguments);";
	return calculer_boucle($id_boucle, $boucles); 
}


?>
