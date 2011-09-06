<?php 

include_spip('inc/presentation');

function gis_form_logo($id_gis){
	include_spip('inc/presentation');
	$editable = false;
	if(autoriser('iconifier', 'gis', $id_gis)){
		$editable = true;
	}
	$iconifier = charger_fonction('iconifier', 'inc');
	$icone = $iconifier('id_gis', $id_gis,'gis_editer', false, $editable);
	return $icone;
}
?>