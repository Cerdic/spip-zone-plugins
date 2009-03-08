<?php
function smush_it($fichier){
	spip_log("SMUSH : appel du filtre smush_it sur $fichier","smush");
	if(file_exists($fichier)){
		spip_log("SMUSH : $fichier existe donc on applique le filtre","smush");
		$smush = charger_fonction('smush_image','inc');
		$smush($fichier);
	}
	return $fichier;
}
?>