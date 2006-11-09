<?php
  /*
   * BOUCLEs Flickr API
   * 
   * Auteur: Mortimer (Pierre Andrews)
   * (c) 2006 - Distribue sous license GNU/GPL
   */
 
include_spip('base/FpipR_db');

include_spip('balise/accuracy');
include_spip('balise/flickr_photo_id');
include_spip('balise/flickr_photo_secret');
include_spip('balise/flickr_token');
include_spip('balise/fpipr_exists');
include_spip('balise/height_photo');
include_spip('balise/id_group');
include_spip('balise/id_photoset');
include_spip('balise/isfamily');
include_spip('balise/isfriend');
include_spip('balise/ispublic');
include_spip('balise/latitude');
include_spip('balise/logo_contact');
include_spip('balise/logo_group');
include_spip('balise/logo_nextphoto');
include_spip('balise/logo_owner');
include_spip('balise/logo_photo');
include_spip('balise/logo_photoset');
include_spip('balise/logo_prevphoto');
include_spip('balise/longitude');
include_spip('balise/nextphoto_id');
include_spip('balise/nextphoto_secret');
include_spip('balise/nextphoto_server');
include_spip('balise/nextphoto_thumb');
include_spip('balise/nextphoto_title');
include_spip('balise/photos_count');
include_spip('balise/prevphoto_id');
include_spip('balise/prevphoto_secret');
include_spip('balise/prevphoto_server');
include_spip('balise/prevphoto_thumb');
include_spip('balise/prevphoto_title');
include_spip('balise/url_group');
include_spip('balise/url_nextphoto');
include_spip('balise/url_owner');
include_spip('balise/url_photo');
include_spip('balise/url_photoset');
include_spip('balise/url_prevphoto');
include_spip('balise/url_userphotos');
include_spip('balise/url_userprofile');
include_spip('balise/width_photo');


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
