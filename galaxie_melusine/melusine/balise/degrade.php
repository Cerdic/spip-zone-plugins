<?php
function balise_DEGRADE($p) {
   return calculer_balise_dynamique($p,DEGRADE,array());
       
}

function balise_DEGRADE_dyn() {
	$i=0;
	while($i<=100){
		$i++;
		$j=100-$i;
		$k=$i-1;
		$opa=0+($j/100);
		$opaie=$j;
		echo "<div class='degrad' style='left:".$k."px;  filter:alpha(opacity=".$opaie."); opacity:".$opa."; '></div>";
	}  
}

?>
