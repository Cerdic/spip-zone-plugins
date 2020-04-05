<?php
function action_clevermail_post_queue_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $pst_id = intval($arg);

  include_spip('inc/autoriser');
  if (autoriser('supprimer','cm_post',$pst_id)) {
    include_spip('inc/clevermail_post_queue');
    clevermail_post_queue($pst_id);
  }
}
?>
