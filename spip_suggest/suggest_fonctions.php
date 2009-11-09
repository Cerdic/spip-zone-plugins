<?php
function spip_suggest_complete ($q) {
	define('_MYSQL_DIR', '/var/lib/mysql/');
	$s = lire_config('spip_suggest/db_name');
	$services = array($s => array('spip_articles' => array('num' => 7)));
	$t = 'spip_articles';
	$num = $services[$s][$t]['num'];
	# query a nettoyer (charset...)
	
	exec($c = 'myisam_suggest '._MYSQL_DIR.$s.'/'.$t.' '.$num.' '.escapeshellarg($q), $lines);
	$res = array();
	foreach (array_map('strtolower',$lines) as $line)
		$res[trim($line)]++;
	foreach (array_map('strtolower',$res) as $key=>$value) {
			echo "$key|$value\n";
	}
}
function spip_suggest_insert_head ($flux) {
	$flux .= '<script src="'.find_in_path("javascript/jquery.autocomplete.js").'" type="text/javascript"></script>';
	$flux .= '
<script>
	$(document).ready(function(){
		function formatItem(row) {
			if (row[1] == 1) {
				return row[0] + " (<strong>" + row[1] + " r&eacute;sultat</strong>)";
			}
			else if (row[1] > 1) {
				return row[0] + " (<strong>" + row[1] + " r&eacute;sultats</strong>)";
			} 
		 }
		$("#recherche").autocomplete("'.generer_url_public("suggest").'", {
			width: 260,
			matchContains: true, 
			selectFirst: false,
			formatItem: formatItem
		}); 
	});
</script>';
  $flux .= '<link rel="stylesheet" href="'.find_in_path("javascript/jquery.autocomplete.css").'" type="text/css" media="all" />';
  return $flux;
}
?>