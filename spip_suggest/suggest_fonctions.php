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
	/*include_spip('inc/recherche');
	if ($s = sql_query("SHOW CREATE TABLE ".table_objet_sql('article'))
	AND $t = sql_fetch($s)
	AND $create = array_pop($t)
	AND preg_match_all('/,\s*FULLTEXT\sKEY.*`(.*)`\s+[(](.*)[)]/i', $create, $keys, PREG_SET_ORDER)) {
		$cles = array();
		foreach ($keys as $key) {
			$cle = $key[2];
			if ($prefix)
				$cle = preg_replace(',`.*`,U', $prefix.'.$0', $cle);
			$cles[$key[1]] = $cle;
		}
	}
	echo fulltext_keys('rubrique', 't');
	var_dump($s);*/
}
function spip_suggest_insert_head ($flux) {
	$flux .= '<script src="'.find_in_path("javascript/jquery.autocomplete.js").'" type="text/javascript"></script>';
	$flux .= '
<script>
  $(document).ready(function(){
  function formatItem(row) {
if (row[0]=="1") {
return row[0] + " (<strong>" + row[1] + " r&eacute;sultat</strong>)";
}
else {
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