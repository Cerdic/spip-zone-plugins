<?php

// Cron
function genie_indexation_dist($t) {
	spip_log('Indexation: cron');
	include_spip('inc/indexation');
	effectuer_une_indexation();
}


?>
