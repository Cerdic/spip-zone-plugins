<?php
function spip_suggest_complete ($q) {
	define('_MYSQL_DIR', '/var/lib/mysql/');
	// lire la base de donnees
	$s = lire_config('spip_suggest/db_name');
	// lire les cles fulltext
	$k = lire_config('spip_suggest/db_keys');

	$tout = array();
	// on parse les donnes pour stocker tout dans un tableau
	foreach(array_filter(explode(',', $k)) as $table) {
		// securite
		$table = explode(':', str_replace(' ', '', $table));
		$tout[$table[0]][] = $table[1];
	}
	// on execute le programme
	foreach ($tout as $key=>$value) {
		foreach($value as $cle) {
			exec($c = 'myisam_suggest '._MYSQL_DIR.$s.'/'.$key.' '.$cle.' '.escapeshellarg($q), $lines);
		}
	}
	
	// les resultats maintenant
	$res = array();
	foreach (array_map('strtolower',$lines) as $line)
		$res[trim($line)]++;
	if (lire_config('spip_suggest/suggest_classement', 'nom') == 'popularite') {
		arsort($res);
	}
	foreach ($res as $key=>$value) {
	  $w .= strtolower($key).'|'.$value."\n";
	}
	return $w;
}
function spip_suggest_insert_head ($flux) {
	$flux .= '<script type="text/javascript" src="'.find_in_path("javascript/jquery.autocomplete.js").'"></script>';
	$flux .= '
<script type="text/javascript">
	$(document).ready(function(){
		function formatItem(row) {
			if (row[1] == 1) {
				return row[0] + " (" + row[1] + " r&eacute;sultat)";
			}
			else if (row[1] > 1) {
				return row[0] + " (" + row[1] + " r&eacute;sultats)";
			} 
		 }
		$("'.lire_config("spip_suggest/suggest_selecteur", "#recherche").'").autocomplete("'.generer_url_public("suggest").'", {
			width: '.lire_config("spip_suggest/suggest_width", 205).',
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