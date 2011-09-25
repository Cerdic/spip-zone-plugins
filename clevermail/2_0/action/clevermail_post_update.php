<?php
function action_clevermail_post_update_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$pst_id = intval($arg);
	
	include_spip('inc/clevermail_post_update');
	$pst_id = clevermail_post_update($pst_id);
}
?>
