<?php
  /*
   * BOUCLEs Flickr API
   * 
   * Auteur: Mortimer (Pierre Andrews)
   * (c) 2006 - Distribue sous license GNU/GPL
   */
 
include_spip('base/FpipR_db');

/*function boucle_DEFAUT($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $type = $boucle->type_requete;	
  
  $f=charger_fonction($type, 'boucle');
  if($f)
	return $f($id_boucle,$boucles);
 
  return boucle_DEFAUT_dist($id_boucle,$boucles);
}*/

spip_include('boucle/flickr_contacts_getlist');
spip_include('boucle/flickr_contacts_getpubliclist');
spip_include('boucle/flickr_favorites_getlist');
spip_include('boucle/flickr_favorites_getpubliclist');
spip_include('boucle/flickr_groups_getinfo');
spip_include('boucle/flickr_groups_pools_getgroups');
spip_include('boucle/flickr_groups_pools_getphotos');
spip_include('boucle/flickr_interestingness_getlist');
spip_include('boucle/flickr_people_getinfo');
spip_include('boucle/flickr_people_getpublicgroups');
spip_include('boucle/flickr_photo_notes');
spip_include('boucle/flickr_photo_tag');
spip_include('boucle/flickr_photo_urls');
spip_include('boucle/flickr_photos_comments_getlist');
spip_include('boucle/flickr_photos_getallcontexts');
spip_include('boucle/flickr_photos_getcontactsphotos');
spip_include('boucle/flickr_photos_getcontactspublicphotos');
spip_include('boucle/flickr_photos_getexif');
spip_include('boucle/flickr_photos_getinfo');
spip_include('boucle/flickr_photos_getnotinset');
spip_include('boucle/flickr_photos_getrecent');
spip_include('boucle/flickr_photos_getuntagged');
spip_include('boucle/flickr_photos_getwithgeodata');
spip_include('boucle/flickr_photos_getwithoutgeodata');
spip_include('boucle/flickr_photos_recentlyupdated');
spip_include('boucle/flickr_photos_search');
spip_include('boucle/flickr_photosets_comments_getlist');
spip_include('boucle/flickr_photosets_getlist');
spip_include('boucle/flickr_photosets_getphotos');
spip_include('boucle/flickr_tags_gethotlist');
spip_include('boucle/flickr_tags_getlistphoto');
spip_include('boucle/flickr_tags_getlistuser');
spip_include('boucle/flickr_tags_getlistuserpopular');
spip_include('boucle/flickr_tags_getlistuserraw');
spip_include('boucle/flickr_tags_getrelated');
spip_include('boucle/flickr_urls_lookupgroup');
spip_include('boucle/flickr_urls_lookupuser');

function critere_tags_dist($idb, &$boucles, $crit) {
}

function critere_tag_mode_dist($idb, &$boucles, $crit) {
}

function critere_text_dist($idb, &$boucles, $crit) {
}

function critere_privacy_filter_dist($idb, &$boucles, $crit) {
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
