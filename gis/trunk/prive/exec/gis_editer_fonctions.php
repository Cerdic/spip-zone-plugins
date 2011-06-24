<?php 

include_spip('inc/presentation');

function gis_form_logo($id_gis){
	include_spip('inc/presentation');
	$editable = false;
	if(autoriser('iconifier', 'gis', $id_gis)){
		$editable = true;
	}
	$iconifier = charger_fonction('iconifier', 'inc');
	spip_log($id_gis,'test');
	$icone = $iconifier('id_gis', $id_gis,'gis', false, $editable);
	return $icone;
}
?>