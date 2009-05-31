<?php
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
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_args = array('user_id','license','upload_date','taken_date');

  $possible_criteres = array('tags','tag_mode','text','privacy_filter',
							 'bbox','accuracy','safe_search','content_type','machine_tags','machine_tag_mode');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $possible_sort = array('date_posted','date_taken','interestingness','relevance');

  $arguments = array_merge(FpipR_utils_search_criteres($boucle,$possible_criteres,$boucles,$id_boucle),
						   FpipR_utils_search_order($boucle,$possible_sort),
						   FpipR_utils_search_args_extras($boucle,$id_table, $possible_args,$possible_extras));
  $boucle->hash = FpipR_utils_calculer_hash('flickr.photos.search',$arguments,$boucle);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
