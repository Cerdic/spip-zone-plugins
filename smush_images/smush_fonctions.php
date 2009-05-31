<?php
function image_smush_it($image){
	spip_log("SMUSH : appel du filtre image_smush_it sur $image","smush");
	$smush = charger_fonction('smush_image','inc');
	$smush($image);
	return $image;
}
?>