<?php 

function genie_echoppe($time){
	$date_premption_panier = date('Y-m-d', strtotime('-2 day')).' 00:00:00';
	sql_updateq("spip_echoppe_paniers",array("statut" => "perime"),"statut = 'temp' AND date <= DATE('".$date_premption_panier."');");
	spip_log("Vidage des paniers temporaires date ref : ".$date_premption_panier."...","echoppe");
}

?>
