<?php
/**
 * 
 * Insertion dans le pipeline i2_cfg_form
 * @return 
 * @param object $flux
 */
function i2_societes_i2_cfg_form($flux){
	$flux .= recuperer_fond('fonds/inscription2_societes');
	return $flux;
}
?>