<?php
  /*
   * BOUCLEs Flickr API
   * 
   * Auteur: Mortimer (Pierre Andrews)
   * (c) 2006 - Distribue sous license GNU/GPL
   */
 
include_spip('base/FpipR_db');

function boucle_DEFAUT($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $type = $boucle->type_requete;	
  
  $f=charger_fonction($type, 'boucle', true);
  if($f)
	return $f($id_boucle,$boucles);
 
  return boucle_DEFAUT_dist($id_boucle,$boucles);
}

function critere_tags_dist($idb, &$boucles, $crit) {
}

function critere_tag_mode_dist($idb, &$boucles, $crit) {
}

function critere_machine_tags_dist($idb, &$boucles, $crit) {
}

function critere_machine_tag_mode_dist($idb, &$boucles, $crit) {
}

function critere_text_dist($idb, &$boucles, $crit) {
}

function critere_privacy_filter_dist($idb, &$boucles, $crit) {
}


function critere_save_search_dist($idb, &$boucles, $crit) {
}

function critere_content_type_dist($idb, &$boucles, $crit) {
}

function critere_bbox_dist($idb, &$boucles, $crit) {
}

function critere_accuracy_dist($idb, &$boucles, $crit) {
}

function critere_min_date_dist($idb, &$boucles, $crit) {
}

//ici, on utilise un critere nsid different pour specifier de qui on veut les contacts
function critere_nsid_dist($idb, &$boucles, $crit) {
}

function critere_just_friends_dist($idb, &$boucles, $crit) {
}

function critere_single_photo_dist($idb, &$boucles, $crit) {
}

function critere_include_self_dist($idb, &$boucles, $crit) {
}

function critere_url_dist($idb, &$boucles, $crit) {
}
function critere_tag_dist($idb, &$boucles, $crit) {
}

function critere_period_dist($idb, &$boucles, $crit) {
}

?>
