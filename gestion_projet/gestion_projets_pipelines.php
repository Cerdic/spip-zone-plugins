<?php

function gestion_projets_header_prive($flux){
$flux .= '
	<script>
	$(document).ready(function() {
	$("#responsecontainer").load("'.find_in_path('inc/timer.php').'");
	[(#ENV{start}|oui)var refreshId = setInterval(function() {
	$("#responsecontainer").load("'.find_in_path('inc/timer.php').'?randval="+ Math.random());
	}, 100);]
	
	//stop the clock when this button is clicked
	$("#stop").click(function()
	{
	clearInterval(refreshId);
	});			
	});
</script>';
return $flux;
}
?>