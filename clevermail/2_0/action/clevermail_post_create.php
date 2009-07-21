<?php
function action_clevermail_post_create_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $lst_id = intval($arg);

  include_spip('inc/autoriser');
  if (autoriser('creer','cm_post',$lst_id)) {
  	include_spip('inc/clevermail_post_create');
    clevermail_post_create($lst_id);
  }
}
?>
