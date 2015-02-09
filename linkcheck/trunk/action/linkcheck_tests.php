<?php
function action_linkcheck_tests_dist(){
	
	include_spip('inc/autoriser');
	include_spip('inc/linkcheck_fcts');
	 
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();

	linkcheck_tests();
	

	if(	defined('_AJAX') && _AJAX) {
		include_spip('linkcheck_fonctions');
		$chiffres=linkcheck_chiffre();
		echo(json_encode($chiffres));
		exit();
	}
	else {
		if ($redirect = _request('redirect')) {
			include_spip('inc/headers');
			redirige_par_entete($redirect.'&message=check_ok');
		}
    }
		
}
?>
