<?php
function rubrique_a_linscription_autoriser(){}
function autoriser_rubrique_a_linscription($faire, $type, $id, $qui,  $opt){
	if ($faire	== 'configurer'){
		include_spip('inc/autoriser');
		return autoriser_configurer_dist($faire,$type,$id,$qui,$opt);	
	}
}
?>