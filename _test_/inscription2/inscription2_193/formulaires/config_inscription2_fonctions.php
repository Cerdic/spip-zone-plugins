<?php
function cfg_config_inscription2_post_traiter(&$cfg){
	$verifier_tables = charger_fonction('inscription2_verifier_tables','inc');
	$verifier_tables();
}
?>
