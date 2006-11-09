<?php
  /*
   * BOUCLEs Flickr API
   * 
   * Auteur: Mortimer (Pierre Andrews)
   * (c) 2006 - Distribue sous license GNU/GPL
   */
 
include_spip('base/FpipR_db');

include_spip('boucle/flickr_contacts_getlist'); 
include_spip('boucle/flickr_contacts_getpubliclist'); 
include_spip('boucle/flickr_favorites_getlist'); 
include_spip('boucle/flickr_favorites_getpubliclist'); 
include_spip('boucle/flickr_groups_getinfo'); 
include_spip('boucle/flickr_groups_pools_getgroups'); 
include_spip('boucle/flickr_groups_pools_getphotos'); 
include_spip('boucle/flickr_interestingness_getlist'); 
include_spip('boucle/flickr_people_getinfo'); 
include_spip('boucle/flickr_people_getpublicgroups'); 
include_spip('boucle/flickr_photo_notes'); 
include_spip('boucle/flickr_photo_tags'); 
include_spip('boucle/flickr_photo_urls'); 
include_spip('boucle/flickr_photos_comments_getlist'); 
include_spip('boucle/flickr_photos_getallcontexts'); 
include_spip('boucle/flickr_photos_getcontactsphotos'); 
include_spip('boucle/flickr_photos_getcontactspublicphotos'); 
include_spip('boucle/flickr_photos_getexif'); 
include_spip('boucle/flickr_photos_getinfo'); 
include_spip('boucle/flickr_photos_getnotinset'); 
include_spip('boucle/flickr_photos_getrecent'); 
include_spip('boucle/flickr_photos_getuntagged'); 
include_spip('boucle/flickr_photos_getwithgeodata'); 
include_spip('boucle/flickr_photos_getwithoutgeodata'); 
include_spip('boucle/flickr_photos_recentlyupdated'); 
include_spip('boucle/flickr_photos_search'); 
include_spip('boucle/flickr_photosets_comments_getlist'); 
include_spip('boucle/flickr_photosets_getinfo'); 
include_spip('boucle/flickr_photosets_getlist'); 
include_spip('boucle/flickr_photosets_getphotos'); 
include_spip('boucle/flickr_tags_gethotlist'); 
include_spip('boucle/flickr_tags_getlistphoto'); 
include_spip('boucle/flickr_tags_getlistuser'); 
include_spip('boucle/flickr_tags_getlistuserpopular'); 
include_spip('boucle/flickr_tags_getlistuserraw'); 
include_spip('boucle/flickr_tags_getrelated'); 
include_spip('boucle/flickr_urls_lookupgroup'); 
include_spip('boucle/flickr_urls_lookupuser'); 

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
