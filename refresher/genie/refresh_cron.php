<?php

function genie_refresh_cron_dist($t) {
	include_spip("inc/refresher_functions");
	
	$res = sql_select("url", "refresher_cron", "last_hit <= DATE_SUB(NOW(), INTERVAL frequence MINUTE)");
	while($row = sql_fetch($res)){
		$url = $row['url'];
		spip_log("Attempt to refresh from cron job ".$url, 'refresher');
		refresh_url($url);
		sql_updateq("refresher_cron", array("last_hit" => 'NOW()'), "url=".sql_quote($url));
	}
	return 1;
}

?>