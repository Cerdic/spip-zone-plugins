<?php
function action_linkcheck_test_dist(){
	
	include_spip('inc/autoriser');
	include_spip('inc/linkcheck_fcts');
 
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();
	
    if (!preg_match(",^\W*(\d+)$,", $arg, $r)) {
        spip_log("action_regler_moderation_dist $arg pas compris");
        return;
    }
 	
	$sel = sql_fetsel('url, distant, etat, id_linkcheck','spip_linkchecks','id_linkcheck='.intval($r[1]));
	
	linkcheck_maj_etat($sel);




		
}
?>
