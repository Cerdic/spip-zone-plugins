<?php
function FpipR_fill_flickr_people_getpublicgroups_dist($arguments) {
  include_spip('inc/flickr_api');
  $groups = flickr_people_getPublicGroups($arguments['user_id'],$arguments['auth_token']);
  FpipR_fill_groups_table($groups,'groups',array('user_id'=>$arguments['user_id']));
}
?>
