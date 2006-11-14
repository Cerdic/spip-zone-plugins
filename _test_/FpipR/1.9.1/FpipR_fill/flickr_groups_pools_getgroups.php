<?php

function FpipR_fill_flickr_groups_pools_getgroups_dist($arguments) {
  include_spip('inc/flickr_api');
  $groups = flickr_groups_pools_getGroups($arguments['page'],$arguments['per_page'],$arguments['auth_token']);
  FpipR_fill_groups_table($groups,'groups');
  return $groups['groups']['total'];
}

?>
