<?php
/*
* BOUCLEs Flickr API
* 
* Auteur: Mortimer (Pierre Andrews)
* (c) 2006 - Distribue sous license GNU/GPL
*/

include_spip('base/FpipR_temporaire');

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

	$possible_args = array('user_id','tags','tag_mode','text','min_upload_date','max_upload_date',
					  'min_taken_date','max_taken_date','license','sort','privacy_filter',
					  'bbox','accuracy','extras','per_page','page');

	$arguments = '';

	//on regarde dans le contexte si les arguments possible sont dispo.
/*	foreach($possible_args as $key) {
		$champ = new Champ;
		$champ->nom_champ = $key;
		$arguments[$key] = calculer_liste(array($champ),array(), $boucles, $boucle->$id_boucle);
	}*/

	//on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
	foreach($boucle->where as $w){
	  $key = str_replace("'",'',$w[1]);
          $key = str_replace("$id_table.",'',$key);
  	  if ($w[0]=="'='" && in_array($key,$possible_args)){
		  switch($w[0]) {
			case "'='":
				$val = str_replace("'",'',$w[2]);
				$arguments[$key] = $val;
			break;
			case "'<'":
			break;
			case "'>'":
			break;
			case "'IN'":
			break;
	 	  }
	  }
	}
	$boucle->hash = "// CREER la table temporaire flickr_photos et la peupler avec le resultat de la query
\$arguments = '';\n";
	foreach($arguments as $key => $val) {
	  if($val) {
	  	$boucle->hash .= "\$arguments['$key']=$val;\n";
	  }
	}
	$boucle->hash .= "fpipr_fill_table_temporaire_boucle('flickr.photos.search',\$arguments);";
	return calculer_boucle($id_boucle, $boucles); 

}


?>