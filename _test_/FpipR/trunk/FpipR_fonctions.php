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

/** boucle FLICKR_PHOTOS_SEARCH
Voir la doc de l'API: http://flickr.com/services/api/flickr.photos.search.html
user_id 
tags 
tag_mode
text
min_upload_date
max_upload_date 
min_taken_date
max_taken_date
license
sort
privacy_filter
bbox
accuracy
extras
per_page
page
*/
function boucle_FLICKR_PHOTOS_SEARCH_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_fpipr_photos";

	$possible_args = array('user_id','license','upload_date','taken_date');

	$possible_criteres = array('tags','tag_mode','text','sort','privacy_filter',
					  'bbox','accuracy','extras','per_page','page');

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
	foreach($arguments as $key => $val) {
	  if($val) {
	  	$boucle->hash .= "\$v=$val;\n";
	  	$boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument($key,\$v);\n";
	  }}

 $boucle->hash .= "FpipR_fill_table_temporaire_boucle('flickr.photos.search',\$arguments);";
	return calculer_boucle($id_boucle, $boucles); 

}


?>
